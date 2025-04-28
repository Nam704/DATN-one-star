<?php

namespace App\Http\Controllers\Client;

use App\Events\OrderNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\NotificationService;
use App\Services\OrderStatusService;

class PaymentController extends Controller
{
    protected $orderStatusService;
    protected $notificationService;

    public function __construct(
        NotificationService $notificationService,
        OrderStatusService $orderStatusService
    ) {
        $this->orderStatusService = $orderStatusService;
        $this->notificationService = $notificationService;
    }

    public function handleVnpayReturn(Request $request)
    {
        $txnRef = $request->get('vnp_TxnRef');

        $order = Order::where('code', $txnRef)->first();

        if (!$order) {
            return redirect()->route('client.user.myAccount')->with('error', 'Đơn hàng không tồn tại.');
        }

        $responseCode = $request->get('vnp_ResponseCode');

        // Ghi giao dịch vào bảng transactions
        Transaction::create([
            'order_id' => $order->id,
            'txn_ref' => $txnRef,
            'amount' => $order->total,
            'status' => $responseCode,
            'payment_method' => 'VNPAY',
        ]);

        if ($responseCode == '00') {
            event(new OrderNotification($order));
            $this->orderStatusService->markVNPAYPaid($order);



            return redirect()->route('client.user.myAccount')->with('success', 'Thanh toán thành công!');
        } else {
            $this->orderStatusService->markVNPAYFailed($order);

            return redirect()->route('client.user.myAccount')->with('error', 'Thanh toán thất bại, vui lòng thử lại.');
        }
    }
}
