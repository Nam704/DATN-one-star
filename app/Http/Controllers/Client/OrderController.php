<?php

namespace App\Http\Controllers\Client;

use App\Events\OrderNotification;
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
        return view('client.user.index', $data);
    }

    public function retryPayment(Request $request, $orderId)
    {
        try {
            $result = $this->retryPaymentService->retryPayment($orderId);
            if ($result['success']) {
                return redirect()->back()->with('success', $result['message']);
            }
            return redirect()->back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('Error in retryPayment', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', $e->getMessage());
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
            event(new OrderNotification($order));
            return redirect()->back()->with('success', 'Yêu cầu hủy đơn hàng đã được gửi.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function check()
    {
        $data = [
            'title' => 'New Order',
            'message' => "New Order, vui lòng kiểm tra và xác nhận!",
            'from_user_id' => null,
            'to_user_id' => null,
            'type' => 'orders',
            'status' => 'unread',
            'goto_id' => null,
        ];
        $this->notificationService->sendPrivate($data);
    }

    public function detailOrder($id)
    {
        $order = Order::findOrFail($id);
        $orderDetails = $order->detailsOrder();
        $orderService = app(\App\Services\OrderService::class);
        return view('client.orders.detail', compact('order', 'orderDetails', 'orderService'));
    }
}
