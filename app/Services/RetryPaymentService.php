<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Order_status;
use Illuminate\Support\Facades\Log;

class RetryPaymentService
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Xử lý yêu cầu thử lại thanh toán từ client.
     *
     * @param int $orderId
     * @return array
     */
    public function retryPayment($orderId)
    {
        // Lấy thông tin đơn hàng
        $order = Order::find($orderId);
        // Log::info($order);
        if (!$order) {
            return ['success' => false, 'message' => 'Đơn hàng không tồn tại.'];
        }
        if (!$order->status) {
            return ['success' => false, 'message' => 'Trạng thái đơn hàng không tồn tại.'];
        }
        // Kiểm tra trạng thái đơn hàng
        if (!in_array($order->status->name, ['Payment Failed', 'Payment Expired'])) {
            return ['success' => false, 'message' => 'Đơn hàng không thể thử lại thanh toán.'];
        }

        // Kiểm tra số lần thử lại thanh toán
        // if ($order->payment_retry_count >= 3) {
        //     return ['success' => false, 'message' => 'Đơn hàng đã thử lại thanh toán quá số lần cho phép.'];
        // }

        // Thực hiện thử lại thanh toán
        try {
            $paymentResult = $this->attemptPayment($order);

            // if ($paymentResult['success']) {
            //     // Cập nhật trạng thái đơn hàng thành "Paid"
            //     $order->payment_retry_count += 1;
            //     $order->order_status_id = Order_status::where('name', 'Paid')->first()->id;
            //     $order->save();
            return ['success' => true, 'message' => 'Thanh toán đã được thử lại thành công.'];
            // } else {
            //     // Cập nhật trạng thái đơn hàng thành "Payment Retry Failed"
            //     $order->payment_retry_count += 1;
            //     $order->order_status_id = Order_status::where('name', 'Payment Failed')->first()->id;
            //     $order->save();

            //     return ['success' => false, 'message' => 'Thử lại thanh toán không thành công.'];
            // }
        } catch (\Exception $e) {
            Log::error("Lỗi khi thử lại thanh toán cho đơn hàng {$order->id}: " . $e->getMessage());
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi thử lại thanh toán.'];
        }
    }

    /**
     * Giả lập việc thử lại thanh toán (gọi API thanh toán).
     *
     * @param Order $order
     * @return array
     */
    private function attemptPayment(Order $order)
    {
        // Giả sử ở đây ta gọi một service thanh toán bên ngoài (ví dụ: VNPAY, Momo, v.v.)
        // Dưới đây là giả lập logic thanh toán
        try {
            return   $paymentResult = $this->gateway_payment($order); // Gọi API thanh toán thử lại.
            // return $paymentResult;
        } catch (\Exception $e) {
            Log::error("Lỗi khi thử lại thanh toán cho đơn hàng {$order->id}: " . $e->getMessage());
            return ['success' => false, 'message' => 'Không thể thực hiện thanh toán lại.'];
        }
    }
    function gateway_payment($order)
    {


        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://127.0.0.1:8000/client/payment";
        $vnp_TmnCode = "ASFZEFO2"; //Mã website tại VNPAY
        $vnp_HashSecret = "1P0E4T01EMVDNJ0EIY4955QEHXK1IH27"; //Chuỗi bí mật

        // $vnp_TxnRef = $_POST['order_id'];//Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_TxnRef = time() . "" . $order->id;
        $vnp_OrderInfo = "Thanh Toán Đơn Hàng";
        $vnp_OrderType = "OneStar";
        $vnp_Amount = $order->total * 100;
        $vnp_Locale = "VN";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

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

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
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

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        );
        // if (isset($_POST['redirect'])) {
        //     header('Location: ' . $vnp_Url);
        //     die();
        // } else {
        //     echo json_encode($returnData);
        // }
        // dùng echo để xuất dữ liệu ngay lập tức ra HTTP response
        if (isset($_POST['redirect'])) {
            echo json_encode([
                'code' => '00',
                'message' => 'success',
                'redirect_url' => $vnp_Url
            ]);
            exit;
        } else {
            echo json_encode([
                'code' => '00',
                'message' => 'success',
                'data' => $vnp_Url
            ]);
            exit;
        }
    }
}
