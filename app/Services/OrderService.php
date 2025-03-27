<?php

namespace App\Services;

use App\Jobs\ExpireOrder;
use App\Models\Order;
use App\Models\Order_status;
use App\Models\OrderCancellation;
use App\Models\OrderCancellationReason;
use App\Models\OrderExpire;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $order;
    protected $orderStatus;
    protected $orderExpire;
    protected $paymentService;
    protected $notificationService;
    protected $orderCancellationReason;
    public function __construct(
        Order $order,
        PaymentService $paymentService,
        Order_status $orderStatus,
        OrderExpire $orderExpire,
        NotificationService $notificationService,
        OrderCancellationReason $orderCancellationReason
    ) {
        $this->orderCancellationReason = $orderCancellationReason;
        $this->orderStatus = $orderStatus;
        $this->orderExpire = $orderExpire;
        $this->paymentService = $paymentService;
        $this->order = $order;
        $this->notificationService = $notificationService;
        // Constructor logic
    }
    function acceptAll($request)
    {
        $orderIds = $request->input('ids');
        foreach ($orderIds as $orderId) {
            $this->updateOrderStatus($orderId);
        }
    }
    function listReason()
    {
        return $this->orderCancellationReason->query()->select('id', 'reason')->orderBy('id', 'DESC')->get();
    }
    public function cancelOrder($request)
    {
        $orderId = $request->input("id_order");
        $reasonId = $request->input("id_reason");

        $order = Order::findOrFail($orderId);

        // Kiểm tra trạng thái hiện tại có thể hủy không
        $nonCancellableStatuses = ['Delivered', 'Cancelled', 'Refunded'];
        if (in_array($order->orderStatus->name, $nonCancellableStatuses)) {
            throw new \Exception("Đơn hàng không thể hủy ở trạng thái hiện tại.");
        }

        // Nếu đã yêu cầu hủy rồi thì không cần xử lý tiếp
        if ($order->orderStatus->name == 'Cancel Requested') {
            return null;
        }

        // Kiểm tra lý do hủy có tồn tại không
        if (!OrderCancellationReason::find($reasonId)) {
            throw new \Exception("Lý do hủy không hợp lệ.");
        }

        // Cập nhật trạng thái thành "Cancel Requested"
        $cancelRequestedId = Order_status::where('name', 'Cancel Requested')->value('id');
        $order->update(['id_order_status' => $cancelRequestedId]);

        // Ghi nhận lý do hủy
        OrderCancellation::create([
            'order_id' => $orderId,
            'reason_id' => $reasonId,
        ]);
        $dataNotification = [
            'title' => 'Update Order',
            'message' => "Update Order, vui lòng kiểm tra và xác nhận!",
            'from_user_id' => $order->id_user,
            'to_user_id' => null,
            'type' => 'orders',
            'status' => 'unread',
            'goto_id' => $order->id,
        ];
        // dd($dataNotification);
        $this->notificationService->sendPrivate($dataNotification);
        return $order;
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
                "id_order_status" => $this->orderStatus->where('name', $data['status'])->first()->id,
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
            $orderExpire = $this->orderExpire->create([
                'id_order' => $order->id,
                // 'expires_at' => Carbon::now()->addMinutes(1),
                'expires_at' => Carbon::now()->addSeconds(3),

            ]);

            // Dispatch the ExpireOrder job
            // trì hoãn 1 phút sau đó thực thi handle job
            ExpireOrder::dispatch($orderExpire->id)->delay($orderExpire->expires_at);
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
