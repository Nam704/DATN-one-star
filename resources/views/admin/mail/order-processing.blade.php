@extends('emails.layouts.email')

@section('title', 'Cập nhật xử lý đơn hàng')

@section('content')
<h2>Đơn hàng của bạn #{{ $order->order_number }} đang được xử lý</h2>

<div class="order-details">
    <p>CHúng toou hiện đang chuẩn bị đơn hàng của bạn để vận chuyển!</p>
    <p><strong>Ngày dự kiến giao hàng:</strong> {{ $order->estimated_shipping_date }}</p>
    <p><strong>Giao hàng ước tính:</strong> {{ $order->estimated_delivery_date }}</p>
</div>

<a href="{{ route('orders.track', $order->order_number) }}" class="button">Theo dõi đơn hàng</a>
@endsection