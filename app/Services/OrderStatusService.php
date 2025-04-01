<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Order_status;
use App\Models\OrderExpire;
use App\Models\Product_variant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    protected $orderExpire;
    public function __construct(OrderExpire $orderExpire)
    {
        $this->orderExpire = $orderExpire;
    }
    /**
     * Cập nhật trạng thái đơn hàng dựa trên phương thức thanh toán
     */
    public function updateInitialStatus(Order $order)
    {
        $method = $order->payment_method;

        if ($method === 'COD') {
            $this->handleCOD($order);
        } elseif ($method === 'VNPAY') {
            $this->handleVNPAY($order);
        } else {
            throw ValidationException::withMessages(['payment_method' => 'Unsupported payment method']);
        }
    }

    protected function handleCOD(Order $order)
    {
        $pending = Order_status::where('name', 'Pending')->first();
        if (!$pending) throw new \Exception('Pending status not found');

        $order->id_order_status = $pending->id;
        $order->payment_status = 'Awaiting Payment';
        $order->save();
    }

    protected function handleVNPAY(Order $order)
    {
        $verification = Order_status::where('name', 'Payment Verification')->first();
        if (!$verification) throw new \Exception('Payment Verification status not found');

        $order->id_order_status = $verification->id;
        $order->payment_status = 'Payment Verification';
        $order->save();
        $orderExpire = $this->orderExpire->create([
            'id_order' => $order->id,
            // 'expires_at' => Carbon::now()->addMinutes(5),
            'expires_at' => Carbon::now()->addSeconds(30),

        ]);

        // Dispatch the ExpireOrder job
        // trì hoãn 1 phút sau đó thực thi handle job
        ExpireOrder::dispatch($orderExpire->id)->delay($orderExpire->expires_at);
    }

    public function markVNPAYPaid(Order $order)
    {
        $paid = Order_status::where('name', 'Paid')->first();
        if (!$paid) throw new \Exception('Paid status not found');

        $order->id_order_status = $paid->id;
        $order->payment_status = 'Paid';
        $order->save();
    }

    public function markVNPAYFailed(Order $order)
    {
        $failed = Order_status::where('name', 'Payment Failed')->first();
        if (!$failed) throw new \Exception('Payment Failed status not found');

        $order->id_order_status = $failed->id;
        $order->payment_status = 'Payment Failed';
        $order->save();
    }

    /**
     * Lấy danh sách các trạng thái có thể chuyển tiếp từ trạng thái hiện tại
     */
    public function getAvailableNextStatuses(Order $order)
    {
        $currentStatus = $order->orderStatus;
        $next = Order_status::find($currentStatus->next_status_id);

        return $next ? [$next] : [];
    }

    /**
     * Kiểm tra xem trạng thái hiện tại có phải là trạng thái kết thúc không
     */
    public function isFinalStatus(Order $order)
    {
        return $order->orderStatus->next_status_id === null;
    }
}
