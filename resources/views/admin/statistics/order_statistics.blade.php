@extends('admin.statistics.dashboard_statistics')
@section('statistics-content')
    <div class="text-center m-4">
        <h1>Thống Kê Đơn Hàng Theo Ngày</h1>
    </div>
    <!-- Row hiển thị 4 card thống kê tổng -->
    <div class="row">
        <!-- Card: Tổng Đơn Hàng -->
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6 class="text-uppercase mt-0">Tổng Đơn Hàng</h6>
                    <h2 class="my-2">{{ number_format($totalOrders) }}</h2>
                </div>
            </div>
        </div>

        <!-- Card: Tổng Đơn Pending -->
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6 class="text-uppercase mt-0">Tổng Đơn Pending</h6>
                    <h2 class="my-2">{{ number_format($pendingOrders) }}</h2>
                </div>
            </div>
        </div>

        <!-- Card: Tổng Đơn Delivered -->
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6 class="text-uppercase mt-0">Tổng Đơn Delivered</h6>
                    <h2 class="my-2">{{ number_format($deliveredOrders) }}</h2>
                </div>
            </div>
        </div>

        <!-- Card: Tổng Đơn Cancelled -->
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6 class="text-uppercase mt-0">Tổng Đơn Cancelled</h6>
                    <h2 class="my-2">{{ number_format($cancelledOrders) }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <!-- Card thống kê chi tiết và biểu đồ -->
            <div class="card">
                <div class="card-body">
                    <!-- Widget Controls -->
                    <div class="card-widgets">
                        <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                        <a data-bs-toggle="collapse" href="#orderStats-collapse" role="button" aria-expanded="false"
                            aria-controls="orderStats-collapse">
                            <i class="ri-subtract-line"></i>
                        </a>
                        <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                    </div>

                    <!-- Card Header -->
                    <h5 class="header-title mb-0">Thống Kê Tình Trạng Đơn Hàng Theo Ngày</h5>


                    <!-- Collapsible Content -->
                    <div id="orderStats-collapse" class="collapse pt-3 show">
                        <div class="row align-items-center mb-4">
                            <!-- Form chọn ngày -->
                            <div class="col-md-8">
                                <form action="{{ route('admin.statistics.dailyStatistics') }}" method="GET"
                                    class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label for="date" class="form-label">Select Date:</label>
                                        <input type="date" id="date" name="date" class="form-control"
                                            value="{{ $date }}">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary w-100">View Statistics</button>
                                    </div>
                                </form>
                            </div>
                            <!-- Hiển thị tổng doanh thu -->
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end align-items-center border-start ps-3">
                                    <div class="text-end">
                                        <p class="text-muted mb-1">Tổng Doanh Thu Trong Ngày</p>
                                        <h3 class="mb-0">{{ number_format($totalRevenue, 0) }}đ</h3>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Biểu Đồ Thống Kê (ApexCharts) -->
                        <div dir="ltr">
                            <div id="order-status-chart" class="apex-charts" data-colors="#3bc0c3,#1a2942,#d1d7d973"></div>
                        </div>

                        <!-- Hàng Tóm Tắt Số Liệu -->
                        <div class="row text-center mt-3">
                            <div class="col">
                                <p class="text-muted">Total Orders</p>
                                <h3 class="mb-0">{{ number_format($totalOrders) }}</h3>
                            </div>
                            <div class="col">
                                <p class="text-muted">Completed Orders</p>
                                <h3 class="mb-0">{{ number_format($completedOrders) }}</h3>
                            </div>
                            <div class="col">
                                <p class="text-muted">Pending Orders</p>
                                <h3 class="mb-0">{{ number_format($pendingOrders) }}</h3>
                            </div>
                            <div class="col">
                                <p class="text-muted">Cancelled Orders</p>
                                <h3 class="mb-0">{{ number_format($cancelledOrders) }}</h3>
                            </div>
                        </div>
                    </div> <!-- End Collapse -->
                </div> <!-- End Card Body -->
            </div> <!-- End Card -->
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-widgets">
                        <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                        <a data-bs-toggle="collapse" href="#cancel-reasons-collapse" role="button" aria-expanded="false"
                            aria-controls="cancel-reasons-collapse">
                            <i class="ri-subtract-line"></i>
                        </a>
                        <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                    </div>
                    <h5 class="header-title mb-3">Lý Do Hủy Đơn</h5>
                    <div id="cancel-reasons-collapse" class="collapse show">
                        @if ($cancelReasons->isNotEmpty())
                            <div class="list-group">
                                @foreach ($cancelReasons as $reason)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-0">{{ $reason->reason }}</h5>
                                                <small class="text-muted">
                                                    Số lượng: {{ number_format($reason->total) }}
                                                </small>
                                            </div>
                                            @if (isset($cancelledProductsGrouped[$reason->reason_id]))
                                                <button class="btn btn-sm btn-outline-primary" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#reason-{{ $reason->reason_id }}"
                                                    aria-expanded="false"
                                                    aria-controls="reason-{{ $reason->reason_id }}">
                                                    Xem chi tiết
                                                </button>
                                            @endif
                                        </div>
                                        @if (isset($cancelledProductsGrouped[$reason->reason_id]))
                                            <div class="collapse mt-2" id="reason-{{ $reason->reason_id }}">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Sản Phẩm</th>
                                                            <th>Số Lượng Hủy</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cancelledProductsGrouped[$reason->reason_id] as $prod)
                                                            <tr>
                                                                <td>{{ $prod->product_name }}</td>
                                                                <td>{{ number_format($prod->quantity) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-muted">Không có dữ liệu lý do hủy cho ngày này.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-widgets">
                        <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                        <a data-bs-toggle="collapse" href="#yearly-sales-collapse" role="button" aria-expanded="false"
                            aria-controls="yearly-sales-collapse"><i class="ri-subtract-line"></i></a>
                        <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                    </div>
                    <h5 class="header-title mb-0">TOP 10 sản phẩm bán chạy trong ngày</h5>

                    <div id="yearly-sales-collapse" class="collapse pt-3 show">
                        @if ($productsSales->isNotEmpty())
                            <ul class="list-group">
                                @foreach ($productsSales as $product)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $product->product_name }}
                                        <span
                                            class="badge bg-primary rounded-pill">{{ number_format($product->total_sold) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>Không có dữ liệu bán hàng cho ngày này.</p>
                        @endif
                    </div>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->


        <div class="col-lg-6">
            <!-- Bảng Top 10 Người Mua Nhiều Nhất Trong Ngày -->
            <div class="card">
                <div class="card-body">
                    <div class="card-widgets">
                        <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                        <a data-bs-toggle="collapse" href="#top-customers-collapse" role="button" aria-expanded="false"
                            aria-controls="top-customers-collapse">
                            <i class="ri-subtract-line"></i></a>
                        <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                    </div>
                    <h5 class="header-title mb-0">TOP 10 Người Mua Nhiều Nhất Trong Ngày</h5>
                    <div id="top-customers-collapse" class="collapse pt-3 show">
                        @if ($topCustomers->isNotEmpty())
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Khách Hàng</th>
                                        <th>Tổng Giá Trị Mua</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topCustomers as $index => $customer)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $customer->user_name ?? 'Khách vãng lai' }}</td>
                                            <td>${{ number_format($customer->total_purchase, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center text-muted">Không có dữ liệu người mua cho ngày này.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Nạp ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Lấy dữ liệu thống kê cho biểu đồ từ controller (truyền qua Blade)
        var orderStatuses = {!! json_encode($statistics->pluck('status')) !!};
        var orderCounts = {!! json_encode($statistics->pluck('total')) !!};

        var options = {
            series: [{
                name: "Order Count",
                data: orderCounts
            }],
            chart: {
                height: 377,
                type: 'bar'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%'
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: orderStatuses
            },
            yaxis: {
                title: {
                    text: "Number of Orders"
                }
            },
            legend: {
                offsetY: 7
            },
            grid: {
                padding: {
                    bottom: 20
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val;
                    }
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#order-status-chart"), options);
        chart.render();
    </script>
@endsection

@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
@endpush
