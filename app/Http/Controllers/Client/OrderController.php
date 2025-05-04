<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_status;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\OrderStatusService;
use App\Services\PaymentService;
use App\Services\RetryPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    protected $orderService;
    protected $paymentService;
    protected $orderStatusService;
    protected $notificationService;
    protected $retryPaymentService;

    public function __construct(
        RetryPaymentService $retryPaymentService,
        OrderService $orderService,
        PaymentService $paymentService,
        NotificationService $notificationService,
        OrderStatusService $orderStatusService
    ) {
        $this->retryPaymentService = $retryPaymentService;
        $this->orderStatusService = $orderStatusService;
        $this->notificationService = $notificationService;
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
    }
    public function updateStatus($orderId)
    {
        try {
            $order = $this->orderService->updateOrderStatus($orderId);
            $this->notificationService->sendAdmin([
                'title' => 'Đơn hàng mới',
                'message' => "Đơn hàng #{$order->code} vừa được tạo. Vui lòng kiểm tra và xử lý.",
                'type' => 'admin',
                'category' => 'order',
                'priority' => 'high',
                'goto_id' => $order->id,
                'goto_route' => 'admin.orders.detail',
                'expires_at' => now()->addDays(7),
            ]);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trạng thái đã đạt tối đa hoặc không thể cập nhật.',
                ], 400);
            }



            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công.',
                'order' => $order,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật trạng thái đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function store(Request $request)
    {
        try {
            if (!session()->has('checkout_data')) {
                throw new \Exception('Dữ liệu thanh toán không tồn tại hoặc đã hết hạn.');
            }

            $response = $this->orderService->handleCreateOrder($request);
            if ($response->getStatusCode() === 201) {
                $data = $response->getData(true);
                if (!isset($data['data']) || !is_array($data['data'])) {
                    throw new \Exception('Dữ liệu đơn hàng từ service không hợp lệ.');
                }
                return response()->json($data, 201);
            }
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in order store', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ] : [],
                'data' => null,
            ], 400);
        }
    }

    public function orders(Request $request)
    {
        $data = $this->orderService->searchOrders($request);

        // Kiểm tra nếu có lỗi validate
        if (isset($data['errors'])) {
            return redirect()->back()->withErrors($data['errors'])->withInput();
        }

        // Đảm bảo các biến mặc định nếu không có dữ liệu
        $data = array_merge([
            'orders' => collect([]), // Trả về collection rỗng nếu không có đơn hàng
            'totalOrders' => 0,
            'openOrders' => 0,
            'averagePrice' => 0,
            'totalRevenue' => 0,
            'groupStatuses' => [],
            'groupStatusCounts' => [],
            'statuses' => [],
        ], $data);

        return view('client.user.index', $data);
    }
    public function retryPayment(Request $request, $orderId)
    {
        try {
            $result = $this->retryPaymentService->retryPayment($orderId);
            if ($result['success']) {
                $data = $result['paymentResult'];
                Log::info('Retry payment successful', ['order_id' => $orderId, 'result' => $result]);
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'code' => $result['code'],
                    'redirectUrl' => $data['redirectUrl'], // URL để chuyển hướng tới VNPAY
                ], 200);
            }

            // Trả về lỗi chi tiết nếu thất bại
            Log::warning('Retry payment failed', ['order_id' => $orderId, 'result' => $result]);
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'code' => $result['code'],
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error in retryPayment', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi không xác định khi thử lại thanh toán: ' . $e->getMessage(),
                'code' => 'UNKNOWN_ERROR',
            ], 500);
        }
    }
    public function cancelOrder(Request $request, $orderId)
    {
        try {
            $request->validate([
                'reason_id' => 'required|exists:order_cancellation_reasons,id',
            ], [
                'reason_id.required' => 'Vui lòng chọn lý do hủy.',
                'reason_id.exists' => 'Lý do hủy không hợp lệ.',
            ]);

            $reasonId = $request->input('reason_id');
            $order = $this->orderService->cancelOrder($orderId, $reasonId);

            return redirect()->back()->with('success', 'Yêu cầu hủy đơn hàng đã được gửi.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }



    public function detailOrder($id)
    {
        $order = Order::findOrFail($id);
        $orderDetails = $order->detailsOrder();
        $orderService = app(\App\Services\OrderService::class);
        return view('client.orders.detail', compact('order', 'orderDetails', 'orderService'));
    }
}
