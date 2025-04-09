<?php

namespace App\Services;

use App\Jobs\ExpireOrder;
use App\Models\Order;
use App\Models\Order_detail;
use App\Models\Order_status;
use App\Models\OrderCancellation;
use App\Models\OrderCancellationReason;
use App\Models\OrderExpire;
use App\Models\Product_variant;
use App\Models\Voucher;
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
    public function createOrder(array $requestData)
    {
        // Bước 1: Chuẩn bị dữ liệu
        try {
            $dataUser = $requestData['data_user'];
            $dataAddress = $requestData['data_address'];
            $dataOrder = $requestData['data_order'];
            $checkoutData = session('checkout_data');
            if (!$checkoutData) {
                throw new \Exception('Không tìm thấy dữ liệu checkout trong session');
            }
            $variants = $checkoutData['variants'];
            $coupon = $checkoutData['coupon'];
        } catch (\Exception $e) {
            throw new \Exception('Lỗi khi chuẩn bị dữ liệu: ' . $e->getMessage());
        }

        // Bước 2: Xử lý voucher (nếu có)
        $voucher = null;
        $discount = 0;
        if ($coupon) {
            try {
                $voucher = Voucher::where('code', $coupon)
                    ->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where('quantity', '>', 0)
                    ->first();

                if (!$voucher) {
                    throw new \Exception('Mã voucher không hợp lệ hoặc đã hết hạn');
                }

                // Tính subtotal trước để áp dụng discount
                $subtotal = 0;
                foreach ($variants as $variant) {
                    if (!isset($variant['price']) || !isset($variant['quantity'])) {
                        throw new \Exception('Dữ liệu biến thể không hợp lệ');
                    }
                    $subtotal += $variant['price'] * $variant['quantity'];
                }

                if ($voucher->type === 'percent') {
                    $discount = ($subtotal * $voucher->discount_amount) / 100;
                    if ($voucher->max_discount_amount && $discount > $voucher->max_discount_amount) {
                        $discount = $voucher->max_discount_amount;
                    }
                } else {
                    $discount = $voucher->discount_amount;
                }

                // Kiểm tra min_amount
                if ($subtotal < $voucher->min_amount) {
                    $discount = 0; // Không áp dụng nếu không đạt min_amount
                }
            } catch (\Exception $e) {
                throw new \Exception('Lỗi khi xử lý voucher: ' . $e->getMessage());
            }
        }

        // Bước 3: Tính subtotal (nếu chưa tính ở trên)
        try {
            $subtotal = $subtotal ?? 0;
            if (!$voucher) {
                foreach ($variants as $variant) {
                    if (!isset($variant['price']) || !isset($variant['quantity'])) {
                        throw new \Exception('Dữ liệu biến thể không hợp lệ');
                    }
                    $subtotal += $variant['price'] * $variant['quantity'];
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('Lỗi khi tính subtotal: ' . $e->getMessage());
        }

        // Bước 4: Tính shipping (giả định = 0)
        $shipping = 0; // Thay đổi logic tính shipping nếu cần

        // Bước 5: Tính total
        $total = $subtotal - $discount + $shipping;

        // Bước 6: Tạo mã đơn hàng unique
        $orderCode = 'ORD-' . uniqid();

        // Bước 7: Lưu thông tin vào bảng orders
        try {
            $order = new Order();
            $order->code = $orderCode;
            $order->id_user = auth()->id() ?? null;
            $order->user_data = json_encode($dataUser);
            $order->address_data = json_encode($dataAddress);
            $order->voucher_data = $voucher ? json_encode($voucher->toArray()) : null;
            $order->note = $dataOrder['order_note'];
            $order->subtotal = $subtotal;
            $order->shipping = $shipping;
            $order->total = $total;
            $order->payment_method = $dataOrder['payment_method'];
            $order->payment_status = 'pending';
            $orderStatus = Order_status::where('name', 'Awaiting Payment')->first();
            if (!$orderStatus) {
                throw new \Exception('Không tìm thấy trạng thái đơn hàng "Awaiting Payment"');
            }
            $order->id_order_status = $orderStatus->id;
            $order->save();
        } catch (\Exception $e) {
            throw new \Exception('Lỗi khi lưu thông tin đơn hàng: ' . $e->getMessage());
        }

        // Bước 8: Lưu thông tin vào bảng order_details
        try {
            foreach ($variants as $variant) {
                $orderDetail = new Order_detail();
                $orderDetail->id_order = $order->id;
                $orderDetail->id_variant = $variant['id_variant'];
                $orderDetail->variant_data = json_encode($variant);
                $orderDetail->quantity = $variant['quantity'];
                $orderDetail->unit_price = $variant['price'];
                $orderDetail->total = $variant['price'] * $variant['quantity'];
                $orderDetail->save();
            }
        } catch (\Exception $e) {
            throw new \Exception('Lỗi khi lưu chi tiết đơn hàng: ' . $e->getMessage());
        }

        // Bước 9: Cập nhật thông tin voucher (nếu có)
        if ($voucher && $discount > 0) {
            try {
                $voucher->total_usage += 1;
                $voucher->quantity -= 1;
                $voucher->save();
            } catch (\Exception $e) {
                throw new \Exception('Lỗi khi cập nhật thông tin voucher: ' . $e->getMessage());
            }
        }

        // Bước 10: Xử lý sau khi tạo đơn hàng
        // Bước 10: Xử lý sau khi tạo đơn hàng
        try {

            session()->forget('checkout_data');
        } catch (\Exception $e) {
            throw new \Exception('Lỗi khi xóa dữ liệu checkout trong session: ' . $e->getMessage());
        }

        return $order; // Trả về đơn hàng vừa tạo
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
                "id_ward" => $data['id_ward'] ?? null,
                "id_voucher" => $data['id_voucher'] ?? null,
            ];
            $order = $this->order->create($dataOrder);
            $order->update(["code" => time() . "" . $order->id]);

            foreach ($data['order_details'] as $item) {
                $variant = Product_variant::where('id', $item['id_variant'])->lockForUpdate()->first();
                if ($variant->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient product quantity");
                }
                $variant->decrement('quantity', $item['quantity']); // giảm sl sản phẩm trong kho
                $order->orderDetails()->create([
                    'id_variant' => $item['id_variant'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);
            }
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
