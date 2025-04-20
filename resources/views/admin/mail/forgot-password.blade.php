@extends('admin.mail.layouts.email')

@section('title', 'Đặt lại mật khẩu của bạn')

@section('content')
<h2>Xin chào! {{ $user->name }} </h2>
<p>Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn</p>

<div class="reset-section">
    <p>Nhấn vào nút bên dưới để đặt lại mật khẩu của bạn:</p>
    <a href="{{ $resetLink }}" class="button">
        <p style="color: white">Đặt lại mật khẩu</p>
    </a>
</div>

<p>Nếu bạn không yêu cầu đặt lại mật khẩu, bạn không cần thực hiện thêm hành động nào nữa</p>

<div class="additional-info">
    <p>Mã thông báo đặt lại mật khẩu này sẽ hết hạn sau 10 phút</p>
    <p>Nếu bạn gặp sự cố khi nhấp vào nút, hãy sao chép và dán url này vào trình duyệt của bạn:</p>
    <p>{{ $resetLink }}</p>
</div>
@endsection