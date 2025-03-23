@extends('client.layouts.home.layout')
@section('title', 'Thông tin tài khoản')

@section('content')
    <!--breadcrumbs area start-->
    <div class="breadcrumbs_area">
        <div class="container">
            <div class="row" style="margin-top: -20px">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="index.html">Trang chủ</a></li>
                            <li>Tài khoản của tôi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--breadcrumbs area end-->

    <!-- my account start  -->
    <section class="main_content_area">
        <div class="container">
            <div class="account_dashboard">
                <div class="row">
                    <div class="col-sm-12 col-md-3 col-lg-3">
                        <!-- Nav tabs -->
                        <div class="dashboard_tab_button">
                            <ul role="tablist" class="nav flex-column dashboard-list" id="nav-tab">
                                <li><a href="#dashboard" data-toggle="tab" class="nav-link active">Bảng điều khiển</a></li>
                                <li> <a href="#orders" data-toggle="tab" class="nav-link">Đơn đặt hàng</a></li>
                                <li><a href="#downloads" data-toggle="tab" class="nav-link">Tải xuống</a></li>
                                <li><a href="#address" data-toggle="tab" class="nav-link">Địa chỉ</a></li>
                                <li><a href="#account-details" data-toggle="tab" class="nav-link">Thông tin tài khoản</a>
                                </li>
                                <li><a href="{{ route('auth.logout') }}" class="nav-link">Đăng xuất</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9 col-lg-9">
                        <!-- Tab panes -->
                        <div class="tab-content dashboard_content">
                            <div class="tab-pane fade show active" id="dashboard">
                                <h3>Bảng điều khiển </h3>
                                <p>Từ bảng điều khiển tài khoản của bạn, bạn có thể dễ dàng kiểm tra và xem các <a
                                        href="#">đơn hàng gần đây</a>, quản lý <a href="#">địa chỉ giao hàng và
                                        thanh toán</a> , cũng như
                                    <a href="#">chỉnh sửa mật khẩu và thông tin tài khoản của mình.</a>

                                </p>
                                @if (auth()->user()->id_role === 1)
                                    <div class="btn-a">
                                        <a href="{{ route('admin.dashboard') }}" class="btn-b">Quản trị website</a>
                                    </div>
                                @endif
                            </div>
                            @include('client.account-setting.tab-panes.order-list')
                            @include('client.account-setting.tab-panes.downloads')
                            @include('client.account-setting.tab-panes.address-list')
                            @include('client.account-setting.tab-panes.account-details')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- my account end   -->


    <!--call to action start-->
    <section class="call_to_action">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="call_action_inner">
                        <div class="call_text">
                            <h3>We Have <span>Recommendations</span> for You</h3>
                            <p>Take 30% off when you spend $150 or more with code Autima11</p>
                        </div>
                        <div class="discover_now">
                            <a href="#">discover now</a>
                        </div>
                        <div class="link_follow">
                            <ul>
                                <li><a href="#"><i class="ion-social-facebook"></i></a></li>
                                <li><a href="#"><i class="ion-social-twitter"></i></a></li>
                                <li><a href="#"><i class="ion-social-googleplus"></i></a></li>
                                <li><a href="#"><i class="ion-social-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--call to action end-->
@endsection
