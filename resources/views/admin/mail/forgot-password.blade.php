@extends('admin.mail.layouts.email')

@section('title', 'Reset Your Password')

@section('content')
<h2>Hello! {{ $user->name }} </h2>
<p>You are receiving this email because we received a password reset request for your account.</p>

<div class="reset-section">
    <p>Click the button below to reset your password:</p>
    <a href="{{ $resetLink }}" class="button">Reset Password</a>
</div>

<p>If you did not request a password reset, no further action is required.</p>

<div class="additional-info">
    <p>This password reset token will expire in 10 minutes.</p>
    <p>If you're having trouble clicking the button, copy and paste this URL into your browser:</p>
    <p>{{ $resetLink }}</p>
</div>
@endsection