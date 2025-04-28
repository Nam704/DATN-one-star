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

    public function canRetryPayment($orderId)
    {
        try {
            $order = Order::lockForUpdate()->findOrFail($orderId);
            $context = ['order_id' => $orderId, 'user_id' => Auth::id()];

            if ($order->id_user !== Auth::id()) {
                $message = "Bạn không có quyền thử lại thanh toán cho đơn hàng {$order->code}.";
                Log::info($message, $context);
                return ['success' => false, 'message' => $message];
            }

            $eligibleOrderStatuses = ['Payment Failed', 'Payment Expired', 'Payment Retry Requested', 'Awaiting Payment'];
            if (!in_array($order->orderStatus->name, $eligibleOrderStatuses)) {
                $message = "Đơn hàng {$order->code} không thể thanh toán lại do trạng thái không hợp lệ ({$order->orderStatus->name}).";
                Log::info($message, $context);
                return ['success' => false, 'message' => $message];
            }

            $allowedMethods = config('payment.allowed_retry_methods', ['VNPAY']);
            if (!in_array($order->payment_method, $allowedMethods)) {
                $message = "Phương thức thanh toán {$order->payment_method} không được hỗ trợ cho thanh toán lại.";
                Log::info($message, $context);
                return ['success' => false, 'message' => $message];
            }

            $retryTimeLimitHours = config('payment.retry_time_limit_hours', 12);
            $timeLimit = Carbon::parse($order->created_at)->addHours($retryTimeLimitHours);
            if (Carbon::now()->greaterThan($timeLimit)) {
                $message = "Đã quá thời gian cho phép ($retryTimeLimitHours giờ) để thanh toán lại đơn hàng {$order->code}.";
                Log::info($message, $context);
                return ['success' => false, 'message' => $message];
            }

            foreach ($order->orderDetails as $detail) {
                $variant = Product_variant::lockForUpdate()->find($detail->id_variant);
                if (!$variant || $variant->quantity < $detail->quantity) {
                    $message = "Sản phẩm trong đơn hàng {$order->code} không còn đủ tồn kho.";
                    Log::info($message, array_merge($context, ['variant_id' => $detail->id_variant]));
                    return ['success' => false, 'message' => $message];
                }
            }

            return ['success' => true, 'message' => 'Đơn hàng đủ điều kiện để thanh toán lại.'];
        } catch (\Exception $e) {
            $message = "Lỗi hệ thống khi kiểm tra thanh toán lại đơn hàng {$orderId}: {$e->getMessage()}";
            Log::error($message, [
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'message' => $message];
        }
    }

    public function retryPayment($orderId)
    {
        return DB::transaction(function () use ($orderId) {
            try {
                $order = Order::lockForUpdate()->findOrFail($orderId);
                $context = ['order_id' => $orderId, 'user_id' => Auth::id()];

                $check = $this->canRetryPayment($orderId);
                if (!$check['success']) {
                    return [
                        'success' => false,
                        'message' => $check['message'],
                        'code' => 'CANNOT_RETRY',
                    ];
                }

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
                        'code' => 'MAX_ATTEMPTS_EXCEEDED',
                    ];
                }

                // Cập nhật trạng thái thành Awaiting Payment
                $awaitingPaymentStatus = Order_status::where('name', 'Awaiting Payment')->first();
                if (!$awaitingPaymentStatus) {
                    throw new \Exception('Trạng thái Awaiting Payment không tồn tại.');
                }
                $order->id_order_status = $awaitingPaymentStatus->id;
                $order->payment_status = 'Awaiting Payment';
                $order->increment('payment_attempts');
                $order->save();

                // Tạo URL thanh toán VNPAY
                $paymentResult = $this->paymentService->vnpay_payment($order);

                if ($paymentResult['code'] === '00') {
                    $message = "Yêu cầu thanh toán lại cho đơn hàng {$order->code} đã được khởi tạo.";
                    Log::info($message, $context);
                    return [
                        'success' => true,
                        'message' => $message,
                        'paymentResult' => $paymentResult,
                        'code' => 'PAYMENT_INITIATED',
                    ];
                } else {
                    $message = "Không thể tạo yêu cầu thanh toán cho đơn hàng {$order->code}: " . ($paymentResult['message'] ?? 'Lỗi không xác định');
                    Log::warning($message, $context);
                    $this->notifyClient($order, 'Yêu cầu thanh toán thất bại', $message);
                    return [
                        'success' => false,
                        'message' => $message,
                        'code' => 'PAYMENT_INITIATION_FAILED',
                    ];
                }
            } catch (\Exception $e) {
                $message = "Lỗi hệ thống khi thử lại thanh toán đơn hàng {$orderId}: {$e->getMessage()}";
                Log::error($message, [
                    'order_id' => $orderId,
                    'user_id' => Auth::id(),
                    'stack_trace' => $e->getTraceAsString(),
                ]);
                $this->notifyClient(null, 'Lỗi hệ thống', 'Có lỗi xảy ra khi thử lại thanh toán. Vui lòng thử lại sau.');
                return [
                    'success' => false,
                    'message' => $message,
                    'code' => 'SYSTEM_ERROR',
                ];
            }
        });
    }

    protected function notifyClient($order, string $title, string $message)
    {

        try {
        } catch (\Exception $e) {
            Log::error("Lỗi khi gửi thông báo trong RetryPaymentService: {$e->getMessage()}", [
                'order_id' => $order ? $order->id : null,
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
        }
    }

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
