<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;

class PaymentController extends Controller
{
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
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing' // Cập nhật trạng thái đơn hàng
            ]);
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
            $order->update(['payment_status' => 'failed']);
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
