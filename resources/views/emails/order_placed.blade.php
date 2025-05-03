<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #1a73e8;">Chúc mừng bạn đã đặt hàng thành công!</h2>
        <p>Kính gửi <strong>{{ $orderDetails['user_name'] }}</strong>,</p>
        <p>Đơn hàng của bạn với mã <strong>#{{ $orderDetails['code'] }}</strong> đã được tạo thành công. Dưới đây là
            thông tin chi tiết:</p>

        <!-- Thông tin đơn hàng -->
        <h3>Thông tin đơn hàng</h3>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Mã đơn hàng:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $orderDetails['code'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Ngày tạo:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    {{ \Carbon\Carbon::parse($orderDetails['created_at'])->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Phương thức thanh toán:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $orderDetails['payment_method'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Trạng thái thanh toán:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $orderDetails['payment_status'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Tổng phụ:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    {{ number_format($orderDetails['subtotal'], 0, ',', '.') }} VNĐ</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Phí vận chuyển:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    {{ number_format($orderDetails['shipping'], 0, ',', '.') }} VNĐ</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Tổng cộng:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    {{ number_format($orderDetails['total'], 0, ',', '.') }} VNĐ</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Ghi chú:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $orderDetails['note'] ?? 'Không có' }}</td>
            </tr>
        </table>

        <!-- Thông tin người dùng -->
        <h3>Thông tin người nhận</h3>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Tên:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $orderDetails['user_name'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Số điện thoại:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $orderDetails['user_phone'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Email:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $orderDetails['user_email'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Địa chỉ:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $orderDetails['address'] }},
                    {{ $orderDetails['ward'] }}, {{ $orderDetails['district'] }}, {{ $orderDetails['province'] }}
                </td>
            </tr>
        </table>

        <!-- Thông tin voucher -->
        <h3>Thông tin khuyến mãi</h3>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Mã voucher:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $orderDetails['voucher_code'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Giảm giá:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    {{ $orderDetails['discount'] === 'N/A' ? 'Không có' : number_format($orderDetails['discount'], 0, ',', '.') . ' VNĐ' }}
                </td>
            </tr>
        </table>

        <!-- Trạng thái đơn hàng -->
        <h3>Trạng thái đơn hàng</h3>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Trạng thái:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $orderDetails['order_status']['name'] }}</td>
            </tr>
        </table>

        <!-- Chi tiết sản phẩm -->
        <h3>Chi tiết sản phẩm</h3>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="padding: 8px; border: 1px solid #ddd;">Sản phẩm</th>
                    <th style="padding: 8px; border: 1px solid #ddd;">Số lượng</th>
                    <th style="padding: 8px; border: 1px solid #ddd;">Đơn giá</th>
                    <th style="padding: 8px; border: 1px solid #ddd;">Tổng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderDetails['order_details'] as $detail)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            {{ $detail['name'] }} (SKU: {{ $detail['sku'] }})<br>
                            @foreach ($detail['attributes'] as $attr)
                                {{ $attr['attribute_name'] }}: {{ $attr['value'] }}<br>
                            @endforeach
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $detail['quantity'] }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            {{ number_format($detail['unit_price'], 0, ',', '.') }} VNĐ</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            {{ number_format($detail['total'], 0, ',', '.') }} VNĐ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>Vui lòng kiểm tra trạng thái đơn hàng trong tài khoản của bạn hoặc liên hệ chúng tôi nếu có thắc mắc.</p>
        <p style="margin-top: 20px;">Trân trọng,<br>Đội ngũ {{ env('APP_NAME') }}</p>
    </div>
</body>

</html>
