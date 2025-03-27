@extends('client.layouts.home.layout')
@section('title', 'profile')
@section('content')
    <section class="main_content_area">
        <div class="container">
            {{-- <a href="{{ route('client.orders.check') }}">Check order</a> --}}
            <div class="account_dashboard">
                <div class="row">
                    <div class="col-sm-12 col-md-3 col-lg-3">
                        <!-- Nav tabs -->
                        <div class="dashboard_tab_button">
                            <ul role="tablist" class="nav flex-column dashboard-list" id="nav-tab">
                                <li> <a href="#orders" data-toggle="tab" class="nav-link active">Orders</a></li>
                                <li><a href="#address" data-toggle="tab" class="nav-link">Addresses</a></li>
                                <li><a href="#account-details" data-toggle="tab" class="nav-link">Account details</a></li>
                                <li><a href="{{ route('auth.logout') }}" class="nav-link">logout</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9 col-lg-9">
                        <!-- Tab panes -->
                        <div class="tab-content dashboard_content">

                            @include('client.user.orders')

                            @include('client.user.address')
                        </div>
                        <div class="tab-pane fade" id="account-details">
                            <h3>Account details </h3>
                            <div class="login">
                                <div class="login_form_container">
                                    <div class="account_login_form">
                                        <form action="#" class="form-details">
                                            <div class="row row-cols-sm-2 row-cols-1">
                                                <div class="mb-2">
                                                    <input type="hidden" value="{{ $user->id }}" id="user_id">
                                                    <label class="form-label" for="FullName">Full Name</label>
                                                    <input type="text" name="name" value="{{ $user->name ?? '' }}"
                                                        id="FullName" class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="Email">Email</label>
                                                    <input type="email" value="{{ $user->email ?? '' }}" name="email"
                                                        id="Email" class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="web-url">Old password</label>
                                                    <input type="text" value="" name="old_password"
                                                        placeholder="Enter your old password" id="web-url"
                                                        class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="phone">Phone</label>
                                                    <input type="text" value="{{ $user->phone ?? '' }}" name="phone"
                                                        id="phone" placeholder="Enter your phone" class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="new_password"> New
                                                        Password</label>
                                                    <input type="password" placeholder="8 - 15 Characters" id="new_password"
                                                        class="form-control" name="new_password">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="new_password_confirmation">Re-Password</label>
                                                    <input type="password" placeholder="8 - 15 Characters"
                                                        name="new_password_confirmation" id="new_password_confirmation"
                                                        class="form-control">
                                                </div>

                                            </div>
                                            <button class="btn btn-primary" type="submit"><i
                                                    class="ri-save-line me-1 fs-16 lh-1"></i> Save</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
@section('scripts')
    @vite('resources/js/address.js')

    {{-- <script src="{{ asset('client/api/accountDetails.js') }}"></script> --}}
    @vite('resources/js/clientDetail.js')
@endsection
