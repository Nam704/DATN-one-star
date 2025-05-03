<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Refund;
use App\Enums\RefundStatus;
use App\Models\Order_status;

class RefundService
{
    // Tạo yêu cầu hoàn tiền
    public function createRefundRequest(Order $order, array $bankDetails): Refund
    {
        if (!$order->isRefundable()) {
            throw new \Exception('Đơn hàng không đủ điều kiện hoàn tiền');
        }

        return Refund::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'bank_details' => $bankDetails,
            'status' => RefundStatus::PENDING,
        ]);
    }

    // Xử lý hoàn tiền thủ công (dành cho quản lý)
    public function processManualRefund(Refund $refund): void
    {
        // Logic hoàn tiền thực tế ở đây (ví dụ: gọi API ngân hàng)
        $refund->update([
            'status' => RefundStatus::COMPLETED,
            'refunded_at' => now(),
        ]);

        // Cập nhật trạng thái đơn hàng
        $refund->order->update(['id_order_status' => Order_status::where('name', 'Refunded')->first()->id]);
    }
}
