@extends('emails.layouts.email')

@section('title', 'Khuyến mãi đặc biệt dành cho bạn!')

@section('content')
<h2>{{ $promotion->title }}</h2>

<div class="promotion-details">
    <p>{{ $promotion->description }}</p>

    @if($promotion->discount_code)
    <div class="discount-code">
        <p><strong>Sử dụng mã:</strong></p>
        <h3>{{ $promotion->discount_code }}</h3>
    </div>
    @endif

    <p><strong>Có hiệu lực đến:</strong> {{ $promotion->end_date }}</p>
</div>

<a href="{{ route('shop.promotions') }}" class="button">Mua sắm ngay</a>
@endsection