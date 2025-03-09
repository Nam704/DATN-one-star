<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $order;
    protected $paymentService;
    public function __construct(Order $order, PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->order = $order;
        // Constructor logic
    }
    function store($data)
    {

        // return DB::transaction(function () use ($data) {
        DB::beginTransaction();
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
        foreach ($data['order_details'] as $item) {
            $order->orderDetails()->create([
                'id_variant' => $item['id_variant'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ]);
        }
        return $order;

        // });
    }
}
