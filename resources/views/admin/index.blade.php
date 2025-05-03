@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Velonic</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li>
                            <li class="breadcrumb-item active">Welcome!</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Welcome!</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xxl-3 col-sm-6">
                <div class="card widget-flat text-bg-primary">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="ri-group-2-line widget-icon"></i>
                        </div>
                        <h6 class="text-uppercase mt-0" title="Customers">Tổng tài khoản</h6>
                        <h2 class="my-2">{{ number_format($countData['user']) }}</h2>
                    </div>
                </div>
            </div> <!-- end col-->

            <div class="col-xxl-3 col-sm-6">
                <div class="card widget-flat text-bg-pink">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="ri-gift-line widget-icon"></i>
                        </div>
                        <h6 class="text-uppercase mt-0" title="Customers">Tổng sản phẩm</h6>
                        <h2 class="my-2">{{ number_format($countData['product']) }}</h2>
                    </div>
                </div>
            </div> <!-- end col-->

            <div class="col-xxl-3 col-sm-6">
                <div class="card widget-flat text-bg-purple">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="ri-wallet-2-line widget-icon"></i>
                        </div>
                        <h6 class="text-uppercase mt-0" title="Customers">Tổng Doanh thu</h6>
                        <h2 class="my-2">{{ number_format($countData['revenue']) }} đ</h2>
                    </div>
                </div>
            </div> <!-- end col-->

            <div class="col-xxl-3 col-sm-6">
                <div class="card widget-flat text-bg-info">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="ri-shopping-basket-line widget-icon"></i>
                        </div>
                        <h6 class="text-uppercase mt-0" title="Customers">Tổng đơn hàng</h6>
                        <h2 class="my-2">{{ number_format($countData['order']) }}</h2>
                    </div>
                </div>
            </div>
            <!-- start thống kê sp -->
        </div>
        <div class="row">
            @include('admin.statistic.components.topsaleproducttoday')
            @include('admin.statistic.components.productcancelled')
            @include('admin.statistic.components.topviewproducttoday')
        </div> <!-- end thống kê sp -->

        <!-- thống kê đơn hàng -->
        <div class="row">
            @include('admin.statistic.components.daly_order')
            @include('admin.statistic.components.weekly_order')
        </div>
        <!-- end thống kê đơn hàng -->
        <div class="row">
            @include('admin.statistic.components.top_user_order')
        </div>

        <!-- end row -->

    </div>
@endsection
@push('styles')
    <x-admin.dashboard-styles />
@endpush
@push('scripts')
    <x-admin.dashboard-scripts />
@endpush
