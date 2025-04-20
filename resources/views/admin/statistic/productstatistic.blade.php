@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Thống kê sản phẩm</h4>
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
                            <i class="ri-shopping-bag-line widget-icon"></i>
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
            </div> <!-- end col-->

        </div>
        
        @include('admin.statistic.components.bieu_do')
        <form action="{{ route('admin.statistics.productStatistics') }}" method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <label for="start_date">Ngày bắt đầu</label>
            <input type="date" name="start_date" id="start_date" value="{{ request('start_date', now()->startOfDay()->toDateString()) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="end_date">Ngày kết thúc</label>
            <input type="date" name="end_date" id="end_date" value="{{ request('end_date', now()->endOfDay()->toDateString()) }}" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
        </div>
    </div>
</form>


        
        <div class="row vudovn">
            <div class="col-xl-6">
                <!-- Todo-->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="p-3">
                            <div class="card-widgets">
                                <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                            </div>
                            <h5 class="header-title mb-0">Top 10 sản phẩm bán tệ</h5>
                            <a href="{{ route('admin.statistics.exportLeastSoldProducts',request()->query()) }}" class="btn btn-primary" style="margin-top: 10px;">
                                   <i class="ri-file-excel-2-line"></i> Xuất Excel
                                </a>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Stt</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Ảnh</th>
                                            <th>Số lượng bán</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topProduct['least_sold_products'] as $key => $product)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>
                                                    <img src="{{ asset( $product->image_primary) }}"
                                                        alt="{{ $product->name }}" width="50">
                                                </td>
                                                <td>{{ number_format($product->total_sold) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                            </div>
                            <h5 class="header-title mb-0">Danh mục sản phẩm</h5>
                            <a href="{{ route('admin.statistics.exportProductsByCategory',request()->query() ) }}" class="btn btn-primary" style="margin-top: 10px;">
                                   <i class="ri-file-excel-2-line"></i> Xuất Excel
                            </a>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Stt</th>
                                            <th>Tên</th>
                                            <th>Tổng sản phẩm</th>
                                            <th>Tổng doanh thu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories_with_revenue as $key => $product)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->total_products }}</td>
                                                <td>{{ number_format($product->total_revenue) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                            </div>
                            <h5 class="header-title mb-0">Sản phẩm sắp hết hàng</h5>
                            <a href="{{ route('admin.statistics.exportLowStockProducts', request()->query()) }}" class="btn btn-primary" style="margin-top: 10px;">
                                   <i class="ri-file-excel-2-line"></i> Xuất Excel
                            </a>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Stt</th>
                                            <th>Tên</th>
                                            <th>Ảnh</th>
                                            <th>Tổng số lượng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($low_stock_products as $key => $product)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>
                                                    <img src="{{ asset( $product->image_primary) }}"
                                                        alt="{{ $product->name }}" width="50">
                                                </td>
                                                <td>{{ number_format($product->total_quantity) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                            </div>
                            <h5 class="header-title mb-0">Top 10 sản phẩm view cao nhất</h5>
                            <a href="{{ route('admin.statistics.exportTopViewProducts') }}" class="btn btn-primary  " style="margin-top: 10px;">
                                   <i class="ri-file-excel-2-line"></i> Xuất Excel
                            </a>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Stt</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Ảnh</th>
                                            <th>Tổng số lượng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($top_view_products as $key => $product)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>
                                                    <img src="{{ asset( $product->image_primary) }}"
                                                        alt="{{ $product->name }}" width="50">
                                                </td>
                                                <td>{{ number_format($product->view) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                            </div>
                            <h5 class="header-title mb-0">Top 10 sản phẩm bình luận nhiều nhất</h5>
                            <a href="{{ route('admin.statistics.exportTopCommentProducts') }}" class="btn btn-primary  " style="margin-top: 10px;">
                                   <i class="ri-file-excel-2-line"></i> Export Excel
                            </a>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Stt</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Ảnh</th>
                                            <th>Tổng số lượng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($top_comment_products as $key => $product)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $product['name'] }}</td>
                                                <td>
                                                    <img src="{{ asset( $product['image_primary']) }}"
                                                        alt="{{ $product['name'] }}" width="50">
                                                </td>
                                                <td>{{ number_format($product['total_comments']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>

        
        <!-- end row -->

    </div>
@endsection
@push('styles')
    <x-admin.dashboard-styles />
    <style>
    .vudovn.row {
    display: flex;
    flex-wrap: wrap;
}

.vudovn .col-xl-6 {
    display: flex;
    flex-direction: column;
}

.vudovn .card {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 350px;
}

.vudovn .card-body {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.vudovn  .table-responsive {
    max-height: 300px;
    overflow-y: auto; 
}

    </style>
@endpush
@push('scripts')
    <x-admin.dashboard-scripts />
@endpush