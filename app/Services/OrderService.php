<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Order_status;
use App\Models\OrderCancellationReason;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $order;
    protected $orderStatus;
    protected $paymentService;
    protected $notificationService;
    protected $orderCancellationReason;
    public function __construct(
        Order $order,
        PaymentService $paymentService,
        Order_status $orderStatus,
        NotificationService $notificationService,
        OrderCancellationReason $orderCancellationReason
    ) {
        $this->orderCancellationReason = $orderCancellationReason;
        $this->orderStatus = $orderStatus;
        $this->paymentService = $paymentService;
        $this->order = $order;
        $this->notificationService = $notificationService;
        // Constructor logic
    }
    function listReason()
    {
        return $this->orderCancellationReason->query()->select('id', 'reason')->orderBy('id', 'DESC')->get();
    }
    public function cancelOrder(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->status == 'canceled' || $order->status == 'completed') {
            return null;
        }

        $reason = OrderCancellationReason::findOrFail($request->reason_id);

        $order->update(['status' => 'canceled']);

        OrderCancellation::create([
            'order_id' => $order->id,
            'reason_id' => $reason->id,
        ]);

        // return response()->json(['message' => 'Đơn hàng đã được hủy thành công', 'reason' => $reason->reason]);
    }
    function listStatus()
    {
        return $this->orderStatus->all();
    }
    public function getOrderDetail($id)
    {

        $order = $this->order->find($id);
        if (!$order) {
            return null;
        }
        return $order->details();
    }
    function store($data)
    {

        return DB::transaction(function () use ($data) {
            // DB::beginTransaction();
            $dataOrder = [

                "id_user" => $data['id_user'] ?? null,
                "user_name" => $data['user_name'],
                "phone_number" => $data['phone_number'],
                "email" => $data['email'] ?? null,
                "id_address" => $data['id_address'],
                "address_detail" => $data['address_detail'] ?? null,
                "note" => $data['note'] ?? null,
                "subtotal" => $data['subtotal'],
                "shipping" => $data['shipping'],
                "total" => $data['total'],
                "payment_method" => $data['payment_method'],
                "payment_status" => $data['payment_status'],
                "id_ward" => $data['id_ward'] ?? null,
                "id_order_status" => $data['id_order_status'],
                "id_voucher" => $data['id_voucher'] ?? null,
            ];
            $order = $this->order->create($dataOrder);
            $order->update(["code" => time() . "" . $order->id]);

            foreach ($data['order_details'] as $item) {
                $order->orderDetails()->create([
                    'id_variant' => $item['id_variant'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);
            }

            $dataNotification = [
                'title' => 'New Order',
                'message' => "New Order, vui lòng kiểm tra và xác nhận!",
                'from_user_id' => $order->id_user,
                'to_user_id' => null,
                'type' => 'orders',
                'status' => 'unread',
                'goto_id' => $order->id,
            ];
            // dd($dataNotification);
            $this->notificationService->sendPrivate($dataNotification);
            return $order;
        });
    }
    public function getOrdersByStatus($id)
    {
        return   $this->order->whereHas('orderStatus', function ($query) use ($id) {
            $query->where('id', $id);
        })->orderByDesc('created_at')->paginate(10);
    }
    public function updateOrderStatus($orderId)
    {
        // $order = $orderId;
        $order = $this->order->findOrFail($orderId);
        $currentStatus = $order->orderStatus;  // Lấy trạng thái hiện tại của đơn hàng

        // // Kiểm tra xem trạng thái hiện tại có trạng thái tiếp theo hợp lệ hay không
        $nextStatus = $currentStatus->nextStatus; // Lấy trạng thái tiếp theo từ bảng order_statuses

        if (!$nextStatus) {
            return null;
        }

        // Cập nhật trạng thái đơn hàng
        $order->id_order_status = $nextStatus->id;
        $order->save();

        return $order;
    }
}