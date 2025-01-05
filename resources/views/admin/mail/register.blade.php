@extends('admin.mail.layouts.email')

@section('title', 'Welcome to Our Store!')

@section('content')
<h2>Hello {{ $userData->name }}!</h2>
<p>Thank you for creating an account with us. Here are your account details:</p>

<div class="account-details">
    <p><strong>Username:</strong> {{ $userData->name }}</p>
    <p><strong>Email:</strong> {{ $userData->email }}</p>
</div>

<p>You can now:</p>
<ul>
    <li>Browse our extensive product catalog</li>
    <li>Save items to your wishlist</li>
    <li>Track your orders</li>
    <li>Get exclusive offers</li>
</ul>

<a href="{{ route('auth.getFormLogin') }}" class="button">Login to Your Account</a>
@endsection