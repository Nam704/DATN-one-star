<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    protected $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
        // Constructor logic
    }
    function store($data)
    {
        $dataOrder = [
            "id_user" => $data['id_user'] ?? null,
            "user_name" => $data['user_name'],
            "phone_number" => $data['phone_number'],
            "email" => $data['email'] ?? null,
            "id_address" => $data['id_address'],
            "total" => $data['total'],
            "id_order_status" => $data['id_order_status'],
            "id_voucher" => $data['id_voucher'] ?? null,
        ];
        $order = $this->order->create($dataOrder);
        // $dataOrderDetails=
        return $order;
    }
}
