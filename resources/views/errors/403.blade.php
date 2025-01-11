@extends('admin.auth.layouts.auth')
@section('title', '403')
@section('content')
<div class="d-flex justify-content-center mb-5">
    <img src="assets/images/svg/404.svg" alt="" class="img-fluid">
</div>

<div class="text-center">
    <h1 class="mb-3">404</h1>
    <h4 class="fs-20">Page not found</h4>
    <p class="text-muted mb-3"> It's looking like you may have taken a wrong
        turn. Don't worry... it happens to the best of us.</p>
</div>

<a href="index.html" class="btn btn-soft-primary w-100"><i class="ri-home-4-line me-1"></i> Back to Home</a>
@endsection