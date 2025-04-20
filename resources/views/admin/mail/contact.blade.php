@extends('admin.mail.layouts.email')

@section('title', 'Tin nhắn liên hệ mới')

@section('content')
<div class="message-box">
    <h3>Chi tiết tin nhắn:</h3>
    <p><strong>Đến từ:</strong> {{ $data->name }}</p>
    <p><strong>Email:</strong> {{ $data->email }}</p>
    <p><strong>Chủ thế:</strong> {{ $data->subject }}</p>

    <div class="message-content">
        <p><strong>Tin nhắn:</strong></p>
        <p>{{ $data->message }}</p>
    </div>

    <div class="timestamp">
        <p><strong>Ngày gửi:</strong> {{ date('Y-m-d H:i:s') }}</p>
    </div>
</div>

<a href="{{ route('admin.contacts.index') }}" class="button">Xem tất cả tin nhắn</a>
@endsection