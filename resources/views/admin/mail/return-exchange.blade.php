@extends('emails.layouts.email')

@section('title', 'Xác nhận yêu cầu trả lại/đổi hàng')

@section('content')
<h2>Xác nhận yêu cầu trả lại/đổi hàng</h2>

<div class="return-details">
    <p>ID yêu cầu: #{{ $return->request_id }}</p>
    <p><strong>Mặt hàng:</strong> {{ $return->product_name }}</p>
    <p><strong>Loại:</strong> {{ $return->request_type }}</p>
    <p><strong>Trạng thái:</strong> Processing</p>
    <p><strong>Thời gian xử lý ước tính:</strong> {{ $return->processing_time }}</p>
</div>

<a href="{{ route('returns.track', $return->request_id) }}" class="button">Theo dõi trạng thái trả lại</a>
@endsection