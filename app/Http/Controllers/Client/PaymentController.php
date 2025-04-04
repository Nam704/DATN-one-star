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
    protected $orderService;
    protected $orderStatusService;
    function __construct(
        NotificationService $notificationService,
        OrderStatusService $orderStatusService
    ) {
        $this->orderStatusService = $orderStatusService;
        $this->notificationService = $notificationService;
    }
    public function handleVnpayReturn(Request $request)
    {
        $orderId = substr($request->get('vnp_TxnRef'), 10); // Cắt bỏ timestamp, lấy ID đơn hàng
        $order = Order::find($orderId);

        if (!$order) {
            // return redirect()->route('client.orders.index')->with('error', 'Đơn hàng không tồn tại.');
            return 'Đơn hàng không tồn tại.' . $orderId;
        }

        // Kiểm tra kết quả thanh toán
        if ($request->get('vnp_ResponseCode') == '00') {

            event(new OrderNotification($order));
            $this->orderStatusService->markVNPAYPaid($order);
            $dataNotification = [
                'title' => 'Update Order Paid',
                'message' => "Update Order Paid, vui lòng kiểm tra và xác nhận!",
                'from_user_id' => $order->id_user,
                'to_user_id' => null,
                'type' => 'orders',
                'status' => 'unread',
                'goto_id' => $order->id,
            ];
            // dd($dataNotification);
            $this->notificationService->sendPrivate($dataNotification);
            Transaction::create(
                [
                    "txn_ref" => $request->get('vnp_TxnRef'),
                    "amount" => $order->total,
                    "status" => $request->get('vnp_ResponseCode'),

                ]

            );

            return redirect()->route('client.user.myAccount')->with('success', 'Thanh toán thành công!');
            // return 'Thanh toán thành công!';
        } else {
            $this->orderStatusService->markVNPAYFailed($order);
            Transaction::create(
                [
                    "txn_ref" => $request->get('vnp_TxnRef'),
                    "amount" => $order->total,
                    "status" => $request->get('vnp_ResponseCode'),

                ]

            );
            // return 'Thanh toán thất bại, vui lòng thử lại.';
            return redirect()->route('client.user.myAccount')->with('error', 'Thanh toán thất bại, vui lòng thử lại.');
        }
    }
}
