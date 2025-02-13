@extends('admin.auth.layouts.auth')

@section('title', 'Reset Password')

@section('content')
<h4 class="fs-20">Reset Password?</h4>

<div>
    @if ($errors->any()||session('error'))
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
            @if (session('error'))
            <li>{{ session('error') }}</li>
            @endif
        </ul>
    </div>
    @endif

</div>
@if (session('success'))
<p class="alert alert-primary">
    {{ session('success') }}
</p>
@endif

<!-- form -->
<form action="{{ route('auth.resetPassword') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="emailaddress" class="form-label">Email address</label>
        <input class="form-control" type="email" name="email" id="emailaddress" required="" readonly
            value="{{ $password_reset->email }}">
    </div>
    <div class="mb-3" type="hidden">
        <input class="form-control" type="hidden" name="password_reset_token" required="" id="token-password" readonly
            value="{{ $token }}">
    </div>
    <div class="mb-3">
        <label for="new_password" class="form-label">Password</label>
        <input class="form-control" type="password" name="new_password" required="" id="new_password"
            placeholder="Enter your new password" value="{{ old('new_password') }}">
    </div>
    <div class="mb-3">
        <label for="re_password" class="form-label">Re_password</label>
        <input class="form-control" type="password" name="re_password" required="" id="re_password"
            placeholder="Enter your new password" value="{{ old('re_password') }}">
    </div>

    <div class="mb-0 text-start">
        <button class="btn btn-soft-primary w-100" type="submit"><i class="ri-loop-left-line me-1 fw-bold"></i>
            <span class="fw-bold">Reset Password</span> </button>
    </div>
</form>
@endsection

@section('bottom-content')
<div class="row">
    <div class="  col-12 text-center d-flex align-items-center justify-content-center">
        <p class="ms-1 text-dark-emphasis ">Back To <a href="{{ route('auth.getFormLogin') }}"
                class="text-dark fw-bold ms-1 link-offset-3 text-decoration-underline"><b>Log In</b></a></p>
        <p class="ms-1 text-dark-emphasis">Don't have an account? <a href="{{ route('auth.getFormRegister') }}"
                class="text-dark fw-bold ms-1 link-offset-3 text-decoration-underline"><b>Sign Up</b></a></p>
        <p class="ms-1 text-dark-emphasis">Back To <a href="{{ route('auth.getFormForgotPassword') }}"
                class="text-dark fw-bold ms-1 link-offset-3 text-decoration-underline"><b>Forgot Password</b></a></p>


    </div>
</div>

@endsection