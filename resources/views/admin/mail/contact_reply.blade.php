@extends('admin.mail.layouts.email')

@section('title', 'Phản hồi từ hệ thống')

@section('content')
    <h2>Xin chào {{ $contact->name }},</h2>
    <p>Bạn đã gửi liên hệ với nội dung:</p>
    <blockquote>{{ $contact->message }}</blockquote>
    <p>Chúng tôi xin phản hồi:</p>
    <blockquote>{{ $contact->reply }}</blockquote>
    <p>Trân trọng,<br>Hệ thống quản lý</p>

    {{-- <a href="{{ route('shop.products') }}" class="button">Continue Shopping</a> --}}
@endsection
