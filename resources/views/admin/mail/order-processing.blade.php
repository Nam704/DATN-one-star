@extends('emails.layouts.email')

@section('title', 'Order Processing Update')

@section('content')
<h2>Your Order #{{ $order->order_number }} is Being Processed</h2>

<div class="order-details">
    <p>We're currently preparing your order for shipping!</p>
    <p><strong>Estimated Shipping Date:</strong> {{ $order->estimated_shipping_date }}</p>
    <p><strong>Estimated Delivery:</strong> {{ $order->estimated_delivery_date }}</p>
</div>

<a href="{{ route('orders.track', $order->order_number) }}" class="button">Track Order</a>
@endsection