<?php

namespace App\Jobs;

use App\Events\OrderNotification;
use App\Models\Order;
use App\Models\OrderExpire;
use App\Models\Order_status;
use App\Models\Product_variant;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderExpireId;

    public function __construct($orderExpireId)
    {
        $this->orderExpireId = $orderExpireId;
    }

    public function handle(NotificationService $notificationService)
    {
        // Tìm bản ghi order_expire theo orderExpireId
        $orderExpire = OrderExpire::find($this->orderExpireId);

        // Kiểm tra nếu orderExpire tồn tại và đã hết hạn
        if ($orderExpire && $orderExpire->expires_at <= now()) {
            // Tìm đơn hàng tương ứng
            $order = Order::find($orderExpire->id_order);

            // Nếu trạng thái đơn hàng không phải là "Paid"
            if ($order->orderStatus->name != 'Paid') {
                // Lấy order status có tên là "Payment Failed"
                $expiredOrderStatus = Order_status::where('name', 'Payment Failed')->first();

                if ($expiredOrderStatus) {
                    // Cập nhật trạng thái đơn hàng thành "Payment Failed"
                    $order->id_order_status = $expiredOrderStatus->id;
                    $order->save();
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
                    $notificationService->sendPrivate($dataNotification);
                    // Gửi sự kiện thông báo về đơn hàng
                    event(new OrderNotification($order));

                    // Giải phóng sản phẩm đã đặt
                    $this->releaseProducts($order);

                    // Xóa bản ghi order_expire
                    $orderExpire->delete();
                }
            }
        }
    }

    /**
     * Giải phóng sản phẩm đã đặt trong đơn hàng
     *
     * @param Order $order
     * @return void
     */
    private function releaseProducts(Order $order)
    {
        foreach ($order->orderDetails as $orderDetail) {
            $variant = Product_variant::find($orderDetail->id_variant);

            if ($variant) {
                // Tăng số lượng sản phẩm trở lại
                $variant->increment('quantity', $orderDetail->quantity);
            }
        }
    }
}
