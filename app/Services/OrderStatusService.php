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
    /**
     * Cập nhật trạng thái đơn hàng dựa trên phương thức thanh toán
     */
    public function updateInitialStatus(Order $order)
    {
        $method = $order->payment_method;

        if ($method === 'COD') {
            return $this->handleCOD($order);
        } elseif ($method === 'VNPAY') {
            return $this->handleVNPAY($order); // Trả về dữ liệu từ handleVNPAY
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

        $order->id_order_status = $status->next_status_id;
        $order->payment_status = 'Payment Verification';
        $order->save();

        $orderExpire = $this->orderExpire->create([
            'id_order' => $order->id,
            'expires_at' => Carbon::now()->addMinutes(1),
        ]);

        // Dispatch the ExpireOrder job
        ExpireOrder::dispatch($orderExpire->id)->delay($orderExpire->expires_at);

        // Gọi vnpay_payment và trả về kết quả
        return $this->paymentService->vnpay_payment($order);
    }

    public function markVNPAYPaid(Order $order)
    {
        $paid = Order_status::where('name', 'Paid')->first();
        if (!$paid) throw new \Exception('Paid status not found');

        $order->id_order_status = $paid->id;
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

    /**
     * Lấy danh sách các trạng thái có thể chuyển tiếp từ trạng thái hiện tại
     */
    public function getAvailableNextStatuses(Order $order)
    {
        $currentStatus = $order->orderStatus;
        $next = Order_status::find($currentStatus->next_status_id);

        return $next ? [$next] : [];
    }

    /**
     * Kiểm tra xem trạng thái hiện tại có phải là trạng thái kết thúc không
     */
    public function isFinalStatus(Order $order)
    {
        return $order->orderStatus->next_status_id === null;
    }
    public function vnpay_payment($order)
    {
        // Cấu hình VNPAY
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://127.0.0.1:8000/client/payment";
        $vnp_TmnCode = "ASFZEFO2"; // Mã website tại VNPAY
        $vnp_HashSecret = "1P0E4T01EMVDNJ0EIY4955QEHXK1IH27"; // Chuỗi bí mật

        // Chuẩn bị dữ liệu cho VNPAY
        $vnp_TxnRef = $order->code;
        $vnp_OrderInfo = "Thanh Toán Đơn Hàng";
        $vnp_OrderType = "OneStar";
        $vnp_Amount = $order->total * 100; // VNPAY yêu cầu số tiền nhân 100
        $vnp_Locale = "VN";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        // Tạo mảng dữ liệu đầu vào
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        // Thêm các tham số tùy chọn nếu có
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        // Sắp xếp mảng dữ liệu theo thứ tự alphabet để tạo chữ ký
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Tạo URL thanh toán VNPAY
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Trả về dữ liệu thay vì echo và exit
        return [
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        ];
    }
}
