@extends('emails.layouts.email')

@section('title', 'Order Shipped')

@section('content')
<h2>Your Order is On Its Way!</h2>

<div class="shipping-details">
    <p>Order #{{ $order->order_number }} has been handed over to our shipping partner.</p>
    <p><strong>Tracking Number:</strong> {{ $order->tracking_number }}</p>
    <p><strong>Expected Delivery:</strong> {{ $order->delivery_date }}</p>
</div>

<a href="{{ $order->tracking_url }}" class="button">Track Shipment</a>
@endsection