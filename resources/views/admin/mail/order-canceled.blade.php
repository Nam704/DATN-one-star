@extends('emails.layouts.email')

@section('title', 'Order Cancellation')

@section('content')
<h2>Order #{{ $order->order_number }} Has Been Canceled</h2>

<div class="cancellation-details">
    <p><strong>Reason:</strong> {{ $order->cancellation_reason }}</p>

    @if($order->refund_amount)
    <p>Refund amount of ${{ number_format($order->refund_amount, 2) }} will be processed within 5-7 business days.</p>
    @endif
</div>

<a href="{{ route('shop.products') }}" class="button">Continue Shopping</a>
@endsection