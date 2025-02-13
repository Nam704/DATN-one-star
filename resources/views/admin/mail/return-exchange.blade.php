@extends('emails.layouts.email')

@section('title', 'Return/Exchange Request Confirmation')

@section('content')
<h2>Return/Exchange Request Received</h2>

<div class="return-details">
    <p>Request ID: #{{ $return->request_id }}</p>
    <p><strong>Item:</strong> {{ $return->product_name }}</p>
    <p><strong>Type:</strong> {{ $return->request_type }}</p>
    <p><strong>Status:</strong> Processing</p>
    <p><strong>Estimated Processing Time:</strong> {{ $return->processing_time }}</p>
</div>

<a href="{{ route('returns.track', $return->request_id) }}" class="button">Track Return Status</a>
@endsection