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
use Illuminate\Support\Facades\Log;

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
        try {
            Log::info('Starting ExpireOrder job', ['orderExpireId' => $this->orderExpireId]);

            // Tìm bản ghi order_expire
            $orderExpire = OrderExpire::find($this->orderExpireId);
            if (!$orderExpire) {
                Log::warning('OrderExpire not found', ['orderExpireId' => $this->orderExpireId]);
                return; // Thoát nếu không tìm thấy
            }

            Log::info('OrderExpire found', ['id' => $orderExpire->id, 'expires_at' => $orderExpire->expires_at]);

            // Kiểm tra nếu đã hết hạn
            if ($orderExpire->expires_at <= now()) {
                // Tìm đơn hàng
                $order = Order::find($orderExpire->id_order);
                if (!$order) {
                    Log::warning('Order not found', ['id_order' => $orderExpire->id_order]);
                    $orderExpire->delete(); // Xóa bản ghi order_expire để tránh lặp lại
                    return;
                }

                Log::info('Order found', ['order_id' => $order->id, 'status' => $order->orderStatus->name ?? 'N/A']);

                // Kiểm tra trạng thái đơn hàng
                if ($order->orderStatus->name != 'Paid') {
                    $expiredOrderStatus = Order_status::where('name', 'Cancelled')->first();
                    if (!$expiredOrderStatus) {
                        Log::error('Order status "Cancelled" not found');
                        return; // Thoát nếu không tìm thấy trạng thái
                    }

                    // Cập nhật trạng thái đơn hàng
                    $order->id_order_status = $expiredOrderStatus->id;
                    $order->save();

                    Log::info('Order status updated', ['order_id' => $order->id, 'new_status' => $expiredOrderStatus->name]);

                    // Gửi thông báo
                    event(new OrderNotification($order));

                    // Giải phóng sản phẩm
                    $this->releaseProducts($order);

                    // Xóa bản ghi order_expire
                    $orderExpire->delete();

                    Log::info('OrderExpire processed successfully', ['orderExpireId' => $this->orderExpireId]);
                } else {
                    Log::info('Order already paid, skipping', ['order_id' => $order->id]);
                    $orderExpire->delete(); // Xóa bản ghi nếu đơn hàng đã thanh toán
                }
            } else {
                Log::info('Order not yet expired', ['expires_at' => $orderExpire->expires_at]);
            }
        } catch (\Exception $e) {
            Log::error('ExpireOrder job failed', [
                'orderExpireId' => $this->orderExpireId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Ném lại ngoại lệ để queue ghi nhận job thất bại
        }
    }

    private function releaseProducts(Order $order)
    {
        foreach ($order->orderDetails as $orderDetail) {
            $variant = Product_variant::find($orderDetail->id_variant);
            if ($variant) {
                $variant->increment('quantity', $orderDetail->quantity);
                Log::info('Product quantity released', [
                    'variant_id' => $variant->id,
                    'quantity' => $orderDetail->quantity
                ]);
            } else {
                Log::warning('Product variant not found', ['id_variant' => $orderDetail->id_variant]);
            }
        }
    }
}
