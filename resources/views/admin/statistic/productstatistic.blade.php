@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
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
                        <h6 class="text-uppercase mt-0" title="Customers">Users</h6>
                        <h2 class="my-2">{{ number_format($countData['user']) }}</h2>
                    </div>
                </div>
            </div> <!-- end col-->

            <div class="col-xxl-3 col-sm-6">
                <div class="card widget-flat text-bg-pink">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="ri-box-line widget-icon"></i>
                        </div>
                        <h6 class="text-uppercase mt-0" title="Customers">Product</h6>
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
                        <h6 class="text-uppercase mt-0" title="Customers">Revenue</h6>
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
                        <h6 class="text-uppercase mt-0" title="Customers">Orders</h6>
                        <h2 class="my-2">{{ number_format($countData['order']) }}</h2>
                    </div>
                </div>
            </div> <!-- end col-->

        </div>


        <div class="row vudovn">
            <div class="col-xl-6">
                <!-- Todo-->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="p-3">
                            <div class="card-widgets">
                                <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                            </div>
                            <h5 class="header-title mb-0">Top sale product</h5>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>Total sold</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topProduct['top_sale_products'] as $key => $product)
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
                            <h5 class="header-title mb-0">Top Least Sold Products</h5>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>Total sold</th>
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
                            <h5 class="header-title mb-0">Low Stock Products</h5>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>Total quanity</th>
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
                            <h5 class="header-title mb-0">Categories With Revenue</h5>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Total product</th>
                                            <th>Total Revenue</th>
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
                            <h5 class="header-title mb-0">Top view products</h5>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>View</th>
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
                            <h5 class="header-title mb-0">Top comment products</h5>
                        </div>
                        <div id="yearly-sales-collapse" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>View</th>
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