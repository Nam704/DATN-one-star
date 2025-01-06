@extends('admin.mail.layouts.email')

@section('title', 'Order Confirmation')

@section('content')
<h2>Thank you for your order!</h2>
<p>Order Number: #{{ $order->order_number }}</p>

<div class="order-details">
    <h3>Order Summary</h3>
    @foreach($order->items as $item)
    <div class="product-item">
        <p>{{ $item->product_name }} x {{ $item->quantity }}</p>
        <p>Price: ${{ number_format($item->price, 2) }}</p>
    </div>
    @endforeach

    <div class="total">
        <p><strong>Total: ${{ number_format($order->total, 2) }}</strong></p>
    </div>
</div>

<div class="shipping-info">
    <h3>Shipping Address</h3>
    <p>{{ $order->shipping_address }}</p>
    <p>Estimated Delivery: {{ $order->estimated_delivery_date }}</p>
</div>

<div class="payment-info">
    <h3>Payment Method</h3>
    <p>{{ $order->payment_method }}</p>
</div>

<a href="{{ route('orders.track', $order->order_number) }}" class="button">Track Order</a>
@endsection