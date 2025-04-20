@extends('admin.mail.layouts.email')

@section('title', 'Chào mừng bạn đến của hàng của chúng tôi!')

@section('content')
<h2>Xin chào {{ $userData->name }}!</h2>
<p>Cảm ơn bạn đã tạo tài khoản với chúng tôi. Sau đây là thông tin chi tiết về tài khoản của bạn:</p>

<div class="account-details">
    <p><strong>Tên đăng nhập:</strong> {{ $userData->name }}</p>
    <p><strong>Email:</strong> {{ $userData->email }}</p>
</div>

<p>Bây giờ bạn có thể:</p>
<ul>
    <li>Duyệt danh mục sản phẩm phong phú của chúng tôi</li>
    <li>Lưu các mục vào danh sách mong muốn của bạn</li>
    <li>Theo dõi đơn hàng của bạn</li>
    <li>Nhận ưu đãi độc quyền</li>
</ul>

<a href="{{ route('auth.getFormLogin') }}" class="button">Đăng nhập vào tài khoản của bạn</a>
@endsection