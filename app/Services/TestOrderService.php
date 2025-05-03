<?php

namespace App\Services;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class TestOrderService
{

    public function createOrder(array $orderData)
    {
        // Validate dữ liệu cơ bản
        if (!isset($orderData['user_data'], $orderData['address_data'], $orderData['products'])) {
            throw new \InvalidArgumentException('Thiếu thông tin bắt buộc');
        }

        return DB::transaction(function () use ($orderData) {
            // 1. Tạo đơn hàng chính
            $order = $this->createMainOrder($orderData);

            // 2. Thêm chi tiết đơn hàng
            $this->createOrderDetails($order->id, $orderData['products']);

            return $order;
        });
    }

    private function createMainOrder(array $data): Order
    {
        // Xác định trạng thái mặc định (ví dụ: 'pending')
        $defaultStatus = OrderStatus::where('name', 'pending')->firstOrFail();

        return Order::create([
            'code' => Str::uuid(), // Tạo mã duy nhất
            'id_user' => $data['user_id'] ?? null,
            'user_data' => $this->formatUserData($data['user_data']),
            'address_data' => $data['address_data'],
            'voucher_data' => $data['voucher_data'] ?? null,
            'note' => $data['note'] ?? null,
            'subtotal' => $this->calculateSubtotal($data['products']),
            'shipping' => $data['shipping'] ?? 0,
            'total' => $this->calculateTotal($data),
            'payment_method' => $data['payment_method'] ?? 'cod',
            'payment_status' => $data['payment_status'] ?? 'pending',
            'id_order_status' => $defaultStatus->id,
        ]);
    }

    private function createOrderDetails(int $orderId, array $products): void
    {
        foreach ($products as $product) {
            OrderDetail::create([
                'id_order' => $orderId,
                'id_variant' => $product['variant_id'] ?? null,
                'variant_data' => $product['variant_data'], // Đảm bảo có snapshot
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
                'total' => $product['quantity'] * $product['unit_price'],
            ]);
        }
    }

    private function formatUserData(array $userData): array
    {
        return [
            'name' => $userData['name'],
            'phone' => $userData['phone'],
            'email' => $userData['email'] ?? null,
        ];
    }

    private function calculateSubtotal(array $products): float
    {
        return array_reduce($products, fn ($carry, $item) => 
            $carry + ($item['unit_price'] * $item['quantity']), 0);
    }

    private function calculateTotal(array $data): float
    {
        $subtotal = $this->calculateSubtotal($data['products']);
        $shipping = $data['shipping'] ?? 0;
        $discount = $this->calculateDiscount($data['voucher_data'] ?? null);

        return max(0, $subtotal + $shipping - $discount);
    }

    private function calculateDiscount(?array $voucherData): float
    {
        if (!$voucherData) return 0;

        // Logic tính toán dựa trên voucher (ví dụ)
        return match ($voucherData['discount_type']) {
            'percentage' => ($voucherData['discount_value'] / 100) * $subtotal,
            'fixed' => $voucherData['discount_value'],
            default => 0
        };
    }
}