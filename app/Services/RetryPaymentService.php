<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Order_status;
use App\Models\Product_variant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RetryPaymentService
{
    protected $paymentService;
    protected $notificationService;
    protected $orderStatusService;

    public function __construct(
        PaymentService $paymentService,
        NotificationService $notificationService,
        OrderStatusService $orderStatusService
    ) {
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
        $this->orderStatusService = $orderStatusService;
    }

    /**
     * Kiểm tra xem đơn hàng có đủ điều kiện để thử lại thanh toán hay không.
     *
     * @param int $orderId
     * @return array ['success' => bool, 'message' => string]
     */
    public function canRetryPayment($orderId)
    {
        try {
            $order = Order::lockForUpdate()->findOrFail($orderId);
            $context = ['order_id' => $orderId, 'user_id' => Auth::id()];

            // Kiểm tra quyền sở hữu
            if ($order->id_user !== Auth::id()) {
                $message = "Bạn không có quyền thử lại thanh toán cho đơn hàng {$order->code}.";
                Log::info($message, $context);
                $this->notifyClient($order, 'Thanh toán lại thất bại', $message);
                return ['success' => false, 'message' => $message];
            }

            // Kiểm tra trạng thái đơn hàng
            $eligibleOrderStatuses = ['Payment Failed', 'Payment Expired', 'Payment Retry Requested'];
            if (!in_array($order->orderStatus->name, $eligibleOrderStatuses)) {
                $message = "Đơn hàng {$order->code} không thể thanh toán lại do trạng thái không hợp lệ ({$order->orderStatus->name}).";
                Log::info($message, $context);
                $this->notifyClient($order, 'Thanh toán lại thất bại', $message);
                return ['success' => false, 'message' => $message];
            }

            // Kiểm tra phương thức thanh toán
            $allowedMethods = config('payment.allowed_retry_methods', ['VNPAY']);
            if (!in_array($order->payment_method, $allowedMethods)) {
                $message = "Phương thức thanh toán {$order->payment_method} không được hỗ trợ cho thanh toán lại.";
                Log::info($message, $context);
                $this->notifyClient($order, 'Thanh toán lại thất bại', $message);
                return ['success' => false, 'message' => $message];
            }

            // Kiểm tra thời gian
            $retryTimeLimitHours = config('payment.retry_time_limit_hours', 12);
            $timeLimit = Carbon::parse($order->created_at)->addHours($retryTimeLimitHours);
            if (Carbon::now()->greaterThan($timeLimit)) {
                $message = "Đã quá thời gian cho phép ($retryTimeLimitHours giờ) để thanh toán lại đơn hàng {$order->code}.";
                Log::info($message, $context);
                $this->notifyClient($order, 'Thanh toán lại thất bại', $message);
                return ['success' => false, 'message' => $message];
            }

            // Kiểm tra tồn kho
            foreach ($order->orderDetails as $detail) {
                $variant = Product_variant::lockForUpdate()->find($detail->id_variant);
                if (!$variant || $variant->quantity < $detail->quantity) {
                    $message = "Sản phẩm trong đơn hàng {$order->code} không còn đủ tồn kho.";
                    Log::info($message, array_merge($context, ['variant_id' => $detail->id_variant]));
                    $this->notifyClient($order, 'Thanh toán lại thất bại', $message);
                    return ['success' => false, 'message' => $message];
                }
            }

            return ['success' => true, 'message' => 'Đơn hàng đủ điều kiện để thanh toán lại.'];
        } catch (\Exception $e) {
            $message = "Lỗi hệ thống khi kiểm tra thanh toán lại đơn hàng {$orderId}: {$e->getMessage()}";
            Log::error($message, [
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'exception' => $e->getTraceAsString(),
            ]);
            $this->notifyClient(null, 'Lỗi hệ thống', 'Có lỗi xảy ra khi kiểm tra thanh toán lại. Vui lòng thử lại sau.');
            return ['success' => false, 'message' => $message];
        }
    }

    /**
     * Xử lý yêu cầu thử lại thanh toán từ client.
     *
     * @param int $orderId
     * @return array
     */
    public function retryPayment($orderId)
    {
        return DB::transaction(function () use ($orderId) {
            try {
                $order = Order::lockForUpdate()->findOrFail($orderId);
                $context = ['order_id' => $orderId, 'user_id' => Auth::id()];

                // Kiểm tra điều kiện thanh toán lại
                $check = $this->canRetryPayment($orderId);
                if (!$check['success']) {
                    return [
                        'success' => false,
                        'message' => $check['message'],
                    ];
                }

                // Kiểm tra số lần thử lại
                $maxAttempts = config('payment.max_retry_attempts', 3);
                if ($order->payment_attempts >= $maxAttempts) {
                    $this->revertStock($order);
                    $order->id_order_status = Order_status::where('name', 'Cancelled')->value('id');
                    $order->payment_status = 'Cancelled';
                    $order->save();

                    $message = "Đơn hàng {$order->code} đã bị hủy do vượt quá số lần thử thanh toán ($maxAttempts lần).";
                    Log::info($message, $context);
                    $this->notifyClient($order, 'Đơn hàng đã bị hủy', $message);

                    return [
                        'success' => false,
                        'message' => $message,
                    ];
                }

                // Chuyển trạng thái sang Awaiting Payment
                $awaitingPaymentStatus = Order_status::where('name', 'Awaiting Payment')->first();
                if (!$awaitingPaymentStatus) {
                    throw new \Exception('Trạng thái Awaiting Payment không tồn tại.');
                }
                $order->id_order_status = $awaitingPaymentStatus->id;
                $order->payment_status = 'Awaiting Payment';
                $order->increment('payment_attempts');
                $order->save();

                // Thực hiện thanh toán
                $paymentResult = $this->paymentService->vnpay_payment($order);

                if ($paymentResult['code'] === '00') {
                    $this->orderStatusService->markVNPAYPaid($order);
                    $message = "Thanh toán thành công cho đơn hàng {$order->code}.";
                    Log::info($message, $context);
                    $this->notifyClient($order, 'Thanh toán thành công', $message);
                    return [
                        'success' => true,
                        'message' => $message,
                        'data' => $paymentResult,
                    ];
                } else {
                    $this->orderStatusService->markVNPAYFailed($order);
                    $message = "Thanh toán thất bại cho đơn hàng {$order->code}: " . ($paymentResult['message'] ?? 'Lỗi không xác định');
                    Log::warning($message, $context);
                    $this->notifyClient($order, 'Thanh toán thất bại', $message);
                    return [
                        'success' => false,
                        'message' => $message,
                    ];
                }
            } catch (\Exception $e) {
                $message = "Lỗi hệ thống khi thử lại thanh toán đơn hàng {$orderId}: {$e->getMessage()}";
                Log::error($message, [
                    'order_id' => $orderId,
                    'user_id' => Auth::id(),
                    'exception' => $e->getTraceAsString(),
                ]);
                $this->notifyClient(null, 'Lỗi hệ thống', 'Có lỗi xảy ra khi thử lại thanh toán. Vui lòng thử lại sau.');
                return [
                    'success' => false,
                    'message' => $message,
                ];
            }
        });
    }

    /**
     * Gửi thông báo cho client.
     *
     * @param Order|null $order
     * @param string $title
     * @param string $message
     * @return void
     */
    protected function notifyClient($order, string $title, string $message)
    {
        $notificationData = [
            'title' => $title,
            'message' => $message,
            'from_user_id' => null,
            'to_user_id' => $order ? $order->id_user : Auth::id(),
            'type' => 'system',
            'status' => 'unread',
            'goto_id' => $order ? $order->id : null,
        ];
        $this->notificationService->sendPrivate($notificationData);
    }

    /**
     * Hoàn kho cho đơn hàng.
     *
     * @param Order $order
     * @return void
     */
    protected function revertStock(Order $order)
    {
        foreach ($order->orderDetails as $detail) {
            $variant = Product_variant::lockForUpdate()->find($detail->id_variant);
            if ($variant) {
                $variant->increment('quantity', $detail->quantity);
                $message = "Hoàn kho cho sản phẩm ID {$detail->id_variant}: Tăng {$detail->quantity} đơn vị.";
                Log::info($message, [
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'variant_id' => $detail->id_variant,
                ]);
            }
        }
    }
}
