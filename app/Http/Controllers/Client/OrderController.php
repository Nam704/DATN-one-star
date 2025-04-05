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
            $dataSession = session('dataCheckout');

            $data = [
                'order' => $dataSession['cart'],
                'details' => $dataSession['details'],
                'user' => $request->input('userData'),
            ];
            $data['order']['payment_method'] = $request->input('payment_method');
            $dataFormatted = [
                "id_user" => $data["order"]["id_user"] ?? null,
                "user_name" => $data["user"]["name"] ?? null,
                "phone_number" => $data["user"]["phone"] ?? null,
                "email" => $data["user"]["email"] ?? null,
                "id_address" => $data["user"]["id_address"] ?? null,
                "address_detail" => $data["user"]["address"] ?? null,
                "note" => $data["user"]["order_note"] ?? null,
                "subtotal" => $data["order"]["subTotal"] ?? 0,
                "shipping" => $data["order"]["shipping"] ?? 0,
                "payment_method" => $data["order"]["payment_method"],
                "id_ward" => $data["user"]["ward"] ?? null,
                "total" => $data["order"]["total"] ?? 0,
                "status" => "Awaiting Payment",
                "id_voucher" => null,
            ];

            $order_details = [];
            foreach ($data["details"] as $item) {
                $order_details[] = [
                    "id_variant" => $item["id_variant"],
                    "quantity" => $item["quantity"],
                    "unit_price" => $item["price"],
                    "total" => $item["product_total"],
                ];
            }
            $dataFormatted['order_details'] = $order_details;
            Log::info($dataFormatted);
            $order = $this->orderService->store($dataFormatted);
            $this->orderStatusService->updateInitialStatus($order);

            $redirect_url = route('client.user.myAccount');
            return response()->json([
                'status' => 'success',
                'redirect_url' => $redirect_url,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
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
    public function detail($id)
    {
        $order = $this->orderService->getOrderDetail($id);
        $listReason = $this->orderService->listReason();
        // return $order;
        return view('client.orders.detail', compact('order', 'listReason'));
        // dd($order);
    }
}
