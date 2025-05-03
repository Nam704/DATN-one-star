<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\NotificationService;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\Log;

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
        $vnp_SecureHash = $request->get('vnp_SecureHash');
        $responseCode = $request->get('vnp_ResponseCode');

        // Xác thực chữ ký VNPAY
        $vnp_HashSecret = "1P0E4T01EMVDNJ0EIY4955QEHXK1IH27"; // Nên lưu trong config
        $inputData = $request->except('vnp_SecureHash');
        ksort($inputData);
        $hashData = "";
        foreach ($inputData as $key => $value) {
            $hashData .= ($hashData ? '&' : '') . urlencode($key) . '=' . urlencode($value);
        }
        $calculatedHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $order = Order::where('code', $txnRef)->first();
        if (!$order) {
            return redirect()->route('client.user.myAccount')->with('error', 'Đơn hàng không tồn tại.');
        }

        // Kiểm tra chữ ký để đảm bảo callback từ VNPAY
        if ($calculatedHash !== $vnp_SecureHash) {
            Log::error('Chữ ký VNPAY không hợp lệ', ['txn_ref' => $txnRef]);
            $this->orderStatusService->markVNPAYFailed($order);
            return redirect()->route('client.user.myAccount')->with('error', 'Giao dịch không hợp lệ.');
        }

        // Ghi giao dịch vào bảng transactions
        Transaction::create([
            'order_id' => $order->id,
            'txn_ref' => $txnRef,
            'amount' => $order->total,
            'status' => $responseCode,
            'payment_method' => 'VNPAY',
        ]);

        // Xử lý kết quả thanh toán
        if ($responseCode == '00') {

            $this->orderStatusService->markVNPAYPaid($order);

            return redirect()->route('client.user.myAccount')->with('success', 'Thanh toán thành công!');
        } else {
            $this->orderStatusService->markVNPAYFailed($order);

            return redirect()->route('client.user.myAccount')->with('error', 'Thanh toán thất bại, vui lòng thử lại.');
        }
    }
}
