<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    protected $paymentService;
    public function __construct(OrderService $orderService, PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
    }
    public function store(Request $request)
    {
        $dataSession = session('dataCheckout');

        $data = [
            'order' => $dataSession['cart'],
            'details' => $dataSession['details'],
            'user' => $request->input('userData'),

        ];
        $data['order']['payment_method'] = $request->input('payment_method');
        $dataFormatted = [

            "id_user" => $data["order"]["id_user"] ?? null,
            "user_name" => $data["user"]["name"] ?? null,
            "phone_number" => $data["user"]["phone"] ?? null,
            "email" => $data["user"]["email"] ?? null,
            "id_address" => $data["user"]["id_address"] ?? null,
            "address_detail" => $data["user"]["address"] ?? null,
            "note" => $data["user"]["order_note"] ?? null,
            "subtotal" => $data["order"]["subTotal"] ?? 0,
            "shipping" => $data["order"]["shipping"] ?? 0,
            "payment_method" => $data["order"]["payment_method"],
            "payment_status" => "pending",
            "id_ward" => $data["user"]["ward"] ?? null,
            "total" => $data["order"]["total"] ?? 0,
            "id_order_status" => 1,
            "id_voucher" => null,
        ];

        $order_details = [];
        foreach ($data["details"] as $item) {
            $order_details[] = [
                "id_variant" => $item["id_variant"],
                "quantity" => $item["quantity"],
                "unit_price" => $item["price"],
                "total" => $item["product_total"],
            ];
        }
        $dataFormatted['order_details'] = $order_details;
        $order = $this->orderService->store($dataFormatted);
        if ($order) {
            if ($order->payment_method == 'vnpay' || $order->payment_status == 'pending') {
                // return response()->json('run payment');

                return $payment = $this->paymentService->vnpay_payment($order);
            } else {
                // return redirect()->route('client.orders.index');
            }
        }

        // return response()->json($order->with('orderDetails')->get());
        // return redirect()->route('client.orders.index');
    }
}
