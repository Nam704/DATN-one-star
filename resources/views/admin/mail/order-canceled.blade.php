@extends('emails.layouts.email')

@section('title', 'Order Cancellation')

@section('content')
<h2>Đơn hàng #{{ $order->order_number }} đã được hủy bỏ</h2>

<div class="cancellation-details">
    <p><strong>Lý do:</strong> {{ $order->cancellation_reason }}</p>

    @if($order->refund_amount)
    <p>Số tiền hoàn lại ${{ number_format($order->refund_amount, 2) }} sẽ được xử lý trong còng 5 ngày làm việc.</p>
    @endif
</div>

<a href="{{ route('shop.products') }}" class="button">Tiếp tục mua hàng</a>
@endsection