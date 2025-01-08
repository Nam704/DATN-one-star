@extends('admin.auth.layouts.auth')

@section('title', 'Register')

@section('content')
<h4 class="fs-20">Free Sign Up</h4>
<p class="text-muted mb-3">Enter your email address and password to access
    account.</p>
<div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

</div>
<!-- form -->
<form action="{{ route('auth.register') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input class="form-control" type="text" id="namee" name="name" placeholder="Enter your name" required="">
    </div>
    <div class="mb-3">
        <label for="emailaddress" class="form-label">Email address</label>
        <input class="form-control" type="email" id="email" name="email" required="" placeholder="Enter your email">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input class="form-control" type="password" name="password" required="" id="password"
            placeholder="Enter your password">
    </div>
    <div class="mb-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="checkbox-signup" name="checkbox-signup">
            <label class="form-check-label" for="checkbox-signup">I accept <a href="javascript: void(0);"
                    class="text-muted">Terms and
                    Conditions</a></label>
        </div>
    </div>
    <div class="mb-0 d-grid text-center">
        <button class="btn btn-primary fw-semibold" type="submit">Sign
            Up</button>
    </div>

    <div class="text-center mt-4">
        <p class="text-muted fs-16">Sign in with</p>
        <div class="d-flex gap-2 justify-content-center mt-3">
            {{-- <a href="javascript: void(0);" class="btn btn-soft-primary"><i class="ri-facebook-circle-fill"></i></a>
            --}}
            <a href="javascript: void(0);" class="btn btn-soft-danger"><i class="ri-google-fill"></i></a>
            {{-- <a href="javascript: void(0);" class="btn btn-soft-info"><i class="ri-twitter-fill"></i></a> --}}
            {{-- <a href="javascript: void(0);" class="btn btn-soft-dark"><i class="ri-github-fill"></i></a> --}}
        </div>
    </div>
</form>
@endsection

@section('bottom-content')
<div class="row">
    <div class="col-12 text-center">
        <p class="text-dark-emphasis">Already have account? <a href="{{ route('auth.getFormLogin') }}"
                class="text-dark fw-bold ms-1 link-offset-3 text-decoration-underline"><b>Log In</b></a>
        </p>
    </div> <!-- end col -->
</div>
@endsection