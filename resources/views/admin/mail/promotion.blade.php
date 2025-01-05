@extends('emails.layouts.email')

@section('title', 'Special Offer for You!')

@section('content')
<h2>{{ $promotion->title }}</h2>

<div class="promotion-details">
    <p>{{ $promotion->description }}</p>

    @if($promotion->discount_code)
    <div class="discount-code">
        <p><strong>Use Code:</strong></p>
        <h3>{{ $promotion->discount_code }}</h3>
    </div>
    @endif

    <p><strong>Valid Until:</strong> {{ $promotion->end_date }}</p>
</div>

<a href="{{ route('shop.promotions') }}" class="button">Shop Now</a>
@endsection