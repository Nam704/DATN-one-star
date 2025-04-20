@extends('emails.layouts.email')

@section('title', 'Đơn hàng đã được giao')

@section('content')
<h2>Đơn hàng của bạn đang trên đường vận chuyển!</h2>

<div class="shipping-details">
    <p>Đơn hàng  #{{ $order->order_number }} đã được chuyển giao cho dododis tác vận chuyển của chúng tôi.</p>
    <p><strong>Số theo dõi:</strong> {{ $order->tracking_number }}</p>
    <p><strong>Dự kiến giao hàng:</strong> {{ $order->delivery_date }}</p>
</div>

<a href="{{ $order->tracking_url }}" class="button">Theo dõi lô hàng</a>
@endsection