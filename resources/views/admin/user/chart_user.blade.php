@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-dark">
                            <i class="mdi mdi-arrow-left-thin"></i>
                            Back
                        </a>
                    </div>
                    <h4 class="page-title">Thống kê tài khoản {{ $user->name }}</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xxl-6 col-sm-6">
                <div class="card widget-flat text-bg-purple">
                    <div class="card-body">
                        <div class="float-end" style="padding-left: 20px">
                            <i class="ri-wallet-2-line widget-icon"></i>
                        </div>
                        <h6 class="text-uppercase mt-0" title="Customers">Tổng chi tiêu</h6>
                        <h2 class="my-2">{{ number_format($totalSpent, 0, ',', '.') }} VNĐ</h2>
                        <p class="mb-0">
                            <span class="badge bg-white bg-opacity-10 me-1">18.25%</span>
                            <span class="text-nowrap">Since last month</span>
                        </p>
                    </div>
                </div>

            </div> <!-- end col-->

            <div class="col-xxl-6 col-sm-6">
                <div class="card widget-flat text-bg-info">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="ri-shopping-basket-line widget-icon"></i>
                        </div>
                        <h6 class="text-uppercase mt-0" title="Customers">Đơn đặt hàng</h6>
                        <h2 class="my-2">{{ $totalOrders }}</h2>
                        <p class="mb-0">
                            <span class="badge bg-white bg-opacity-25 me-1">-5.75%</span>
                            <span class="text-nowrap">Since last month</span>
                        </p>
                    </div>
                </div>
            </div> <!-- end col-->

        </div>
        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Simple Donut Chart</h4>
                        <div dir="ltr">
                            <div id="simple-donut" class="apex-charts" data-colors="#3bc0c3,#6c757d,#4489e4,#d03f3f,#edc755"></div>
                        </div>
                    </div>
                    <!-- end card body-->
                </div>
                <!-- end card -->
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-lg-8">

            </div> <!-- end col-->
            <div class="col-lg-4">

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <h4 class="fs-22 fw-semibold">69.25%</h4>
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> US Dollar Share</p>
                            </div>
                            <div class="flex-shrink-0">
                                <div id="us-share-chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div> <!-- end card-->
            </div> <!-- end col-->

        </div> --}}
        <!-- end row -->

        <div class="row">
            <div class="col-xl-6">
                <!-- Chat-->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="p-3">
                            <div class="card-widgets">
                                <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                                <a data-bs-toggle="collapse" href="#yearly-sales-collapse" role="button"
                                    aria-expanded="false" aria-controls="yearly-sales-collapse"><i
                                        class="ri-subtract-line"></i></a>
                                <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                            </div>
                            <h5 class="header-title mb-0">Đánh giá</h5>
                        </div>


                    </div>

                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-xl-6">
                <!-- Todo-->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="p-3">
                            <div class="card-widgets">
                                <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                                <a data-bs-toggle="collapse" href="#yearly-sales-collapse" role="button"
                                    aria-expanded="false" aria-controls="yearly-sales-collapse"><i
                                        class="ri-subtract-line"></i></a>
                                <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                            </div>
                            <h5 class="header-title mb-0">Đánh giá</h5>
                        </div>


                    </div>
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
        <!-- end row -->

    </div>
@endsection

@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
@endpush
