<?php

namespace App\Http\Controllers\Client;

use App\Events\OrderNotification;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\OrderStatusService;
use App\Services\PaymentService;
use App\Services\RetryPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    public function retryPayment(Request $request)
    {
        $orderId = $request->input('order_id');
        // Log::info($orderId);
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $result =   $this->retryPaymentService->retryPayment($orderId);
        return response()->json($result);
    }
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $order = $this->orderService->createOrder($data);
            return response()->json([
                'status' => 200,
                'message' => 'Đặt hàng thành công',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),

            ], 500);
        }
    }
    public function cancel(Request $request)
    {
        $order = $this->orderService->cancelOrder($request);
        event(new OrderNotification($order));

        return response()->json([
            'status' => 200,
            'message' => 'Đã hủy đơn hàng',

        ]);
    }
    function  check()
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
        // Lấy đơn hàng với dữ liệu tối ưu từ hàm details
        $order = Order::findOrFail($id)->detailsOrder();
        // return $order;
        return view('client.orders.detail', ['order' => $order]);
    }
}
