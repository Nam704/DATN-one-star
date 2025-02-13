@extends('admin.auth.layouts.auth')

@section('title', 'Login')

@section('content')
<h4 class="fs-20">Sign In</h4>
<p class="text-muted mb-3">Enter your email address and password to access
    account.
</p>
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
<form action="{{ route('auth.login') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="emailaddress" class="form-label">Email address</label>
        <input class="form-control" type="email" name="email" id="emailaddress" required=""
            placeholder="Enter your email">
    </div>
    <div class="mb-3">
        <a href="{{ route('auth.getFormForgotPassword') }}" class="text-muted float-end"><small>Forgot
                your
                password?</small></a>
        <label for="password" class="form-label">Password</label>
        <input class="form-control" type="password" name="password" required="" id="password"
            placeholder="Enter your password">
    </div>
    <div class="mb-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember">
            <label class="form-check-label" for="checkbox-signin">Remember
                me</label>
        </div>
    </div>
    <div class="mb-0 text-start">
        <button class="btn btn-soft-primary w-100" type="submit"><i class="ri-login-circle-fill me-1"></i> <span
                class="fw-bold">Log
                In</span> </button>
    </div>

    <div class="text-center mt-4">
        <p class="text-muted fs-16">Sign in with</p>
        <div class="d-flex gap-2 justify-content-center mt-3">
            {{-- <a href="javascript: void(0);" class="btn btn-soft-primary"><i class="ri-facebook-circle-fill"></i></a>
            --}}
            <a href="javascript: void(0);" class="btn btn-soft-danger"
                onclick="window.location.href='{{ route('auth.google') }}'"><i class="ri-google-fill"></i></a>
            {{-- <a href="javascript: void(0);" class="btn btn-soft-info"><i class="ri-twitter-fill"></i></a>
            <a href="javascript: void(0);" class="btn btn-soft-dark"><i class="ri-github-fill"></i></a> --}}
        </div>
    </div>
</form>
@endsection

@section('bottom-content')
<div class="row">
    <div class="col-12 text-center">
        <p class="text-dark-emphasis">Don't have an account? <a href="{{ route('auth.getFormRegister') }}"
                class="text-dark fw-bold ms-1 link-offset-3 text-decoration-underline"><b>Sign up</b></a>
        </p>
    </div> <!-- end col -->
</div>
@endsection