<?php

namespace App\Services;

use App\Jobs\ExpireOrder;
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
    protected $paymentService;

    public function __construct(OrderExpire $orderExpire, PaymentService $paymentService)
    {
        $this->orderExpire = $orderExpire;
        $this->paymentService = $paymentService;
    }

    public function updateInitialStatus(Order $order)
    {
        $method = $order->payment_method;

        if ($method === 'COD') {
            return $this->handleCOD($order);
        } elseif ($method === 'VNPAY') {
            return $this->handleVNPAY($order);
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
        $redirectUrl = route('client.user.myAccount');
        return [
            'code' => '00',
            'message' => 'success',
            'redirectUrl' => $redirectUrl
        ];
    }

    protected function handleVNPAY(Order $order)
    {
        $status = Order_status::where('name', 'Awaiting Payment')->first();
        if (!$status) throw new \Exception('Awaiting Payment status not found');

        $order->id_order_status = $status->id;
        $order->payment_status = 'Awaiting Payment';
        $order->save();

        $orderExpire = $this->orderExpire->create([
            'id_order' => $order->id,
            // 'expires_at' => Carbon::now()->addMinutes(15),
            'expires_at' => Carbon::now()->addSeconds(10),


        ]);

        ExpireOrder::dispatch($orderExpire->id)->delay($orderExpire->expires_at);
        return $this->paymentService->vnpay_payment($order);
    }

    public function markVNPAYPaid(Order $order)
    {
        $paid = Order_status::where('name', 'Paid')->first();
        if (!$paid) throw new \Exception('Paid status not found');

        $order->id_order_status = $paid->next_status_id;
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

    public function getAvailableNextStatuses(Order $order)
    {
        $currentStatus = $order->orderStatus;
        $next = Order_status::find($currentStatus->next_status_id);
        return $next ? [$next] : [];
    }

    public function isFinalStatus(Order $order)
    {
        return $order->orderStatus->next_status_id === null;
    }
}
