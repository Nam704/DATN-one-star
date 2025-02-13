@extends('admin.auth.layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<h4 class="fs-20">Forgot Password?</h4>
<p class="text-muted mb-3">Enter your email address and we'll send you an email
    with instructions to reset your password.</p>
<div>
    @if (session('success'))
    <p class="alert alert-primary">
        {{ session('success') }}
    </p>
    @endif
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

<!-- form -->
<form action="{{ route('auth.sendPasswordResetEmail') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="emailaddress" class="form-label">Email address</label>
        <input class="form-control" name="email" type="email" id="emailaddress" required=""
            placeholder="Enter your email">
    </div>

    <div class="mb-0 text-start">
        <button class="btn btn-soft-primary w-100" type="submit"><i class="ri-loop-left-line me-1 fw-bold"></i> <span
                class="fw-bold">Reset Password</span> </button>
    </div>
</form>
@endsection

@section('bottom-content')
<div class="row">
    <div class="col-12 text-center">
        <p class="text-dark-emphasis">Back To <a href="{{ route('auth.getFormLogin') }}"
                class="text-dark fw-bold ms-1 link-offset-3 text-decoration-underline"><b>Log In</b></a></p>
    </div> <!-- end col -->
</div>
@endsection