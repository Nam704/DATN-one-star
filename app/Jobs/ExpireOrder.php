<?php

namespace App\Jobs;

use App\Events\OrderNotification;
use App\Models\Order;
use App\Models\OrderExpire;
use App\Models\Order_status;
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

    public function handle()
    {
        $orderExpire = OrderExpire::find($this->orderExpireId);

        if ($orderExpire && $orderExpire->expires_at <= now()) {
            $order = Order::find($orderExpire->id_order);

            if ($order) {
                // Lấy order status có name là expired.
                $expiredOrderStatus = Order_status::where('name', 'Payment Failed')->first();

                if ($expiredOrderStatus) {
                    $order->id_order_status = $expiredOrderStatus->id;
                    $order->save();
                    event(new OrderNotification($order));
                    // Các xử lý khác:
                    // - Gửi email thông báo hết hạn
                    // - Giải phóng sản phẩm
                    $orderExpire->delete(); // Xóa bản ghi order_expire.
                }
            }
        }
    }
}
