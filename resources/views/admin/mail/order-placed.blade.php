@extends('admin.mail.layouts.email')

@section('title', 'Xác nhận đơn hàng')

@section('content')
<h2>Cảm ơn bạn đã đặt hàng!</h2>
<p>Order Number: #{{ $order->order_number }}</p>

<div class="order-details">
    <h3>Tóm tắt đơn hàng</h3>
    @foreach($order->items as $item)
    <div class="product-item">
        <p>{{ $item->product_name }} x {{ $item->quantity }}</p>
        <p>Giá: ${{ number_format($item->price, 2) }}</p>
    </div>
    @endforeach

    <div class="total">
        <p><strong>Tổng: ${{ number_format($order->total, 2) }}</strong></p>
    </div>
</div>

<div class="shipping-info">
    <h3>Địa chỉ người gửi hàng</h3>
    <p>{{ $order->shipping_address }}</p>
    <p>Dự kiến giao hàng: {{ $order->estimated_delivery_date }}</p>
</div>

<div class="payment-info">
    <h3>Phương thức thanh toán</h3>
    <p>{{ $order->payment_method }}</p>
</div>

<a href="{{ route('orders.track', $order->order_number) }}" class="button">Theo dõi đơn hàng</a>
@endsection