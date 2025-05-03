<?php

namespace App\Services;

class PaymentService
{
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
            'redirectUrl' => $vnp_Url
        ];
    }
}
