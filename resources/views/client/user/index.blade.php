@extends('client.layouts.home.layout')
@section('title', 'profile')
@section('content')
    <section class="main_content_area">
        <div class="container">
            {{-- <a href="{{ route('client.orders.check') }}">Check order</a> --}}
            <div class="account_dashboard">
                <div class="row">
                    <div class="col-12">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Tab điều hướng nằm ngang -->
                        <ul role="tablist" class="nav nav-tabs mb-3" id="nav-tab">
                            <li class="nav-item">
                                <a href="#orders" data-toggle="tab" class="nav-link active">Đơn hàng</a>
                            </li>
                            <li class="nav-item">
                                <a href="#address" data-toggle="tab" class="nav-link">Địa chỉ</a>
                            </li>
                            <li class="nav-item">
                                <a href="#account-details" data-toggle="tab" class="nav-link">Chi tiết tài khoản</a>
                            </li>
                            <li class="nav-item">
                                <a href="#voucher" data-toggle="tab" class="nav-link">Mã giảm giá</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                            </li>
                            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </ul>

                        <!-- Nội dung tab -->
                        <div class="tab-content dashboard_content">
                            @include('client.user.orders')
                            @include('client.user.address')
                            @include('client.user.accountDetails')
                            @include('client.user.voucher')
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
