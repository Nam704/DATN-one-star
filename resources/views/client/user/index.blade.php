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
                            @include('client.user.accountDetails')
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
