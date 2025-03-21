@extends('admin.statistics.dashboard_statistics')
@section('statistics-content')
<div class="container-fluid">
    <!-- Header & Thông tin tháng -->
    <div class="text-center my-4">
        <h1>Thống Kê Đơn Hàng Theo Tháng</h1>
        <p>
            Từ ngày: <strong>{{ $startOfMonth->format('d/m/Y') }}</strong> đến <strong>{{ $endOfMonth->format('d/m/Y') }}</strong>
        </p>
        <p>
            Giá trị trung bình mỗi đơn hàng (AOV): <strong>${{ number_format($averageOrderValue, 2) }}</strong>
        </p>
    </div>

    <!-- Form chọn tháng -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('admin.thongke.monthlyStatistics') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="month" class="form-label">Chọn Tháng (YYYY-MM):</label>
                    <input type="month" id="month" name="month" class="form-control" value="{{ $selectedMonth->format('Y-m') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Xem thống kê</button>
                </div>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex justify-content-end align-items-center border-start ps-3">
                <div class="text-end">
                    <p class="text-muted mb-1">Tổng Doanh Thu Tháng</p>
                    <h3 class="mb-0">${{ number_format($totalRevenue, 0) }}đ</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Tổng Đơn Hàng</h5>
                    <p class="card-text">{{ $allTotalOrders }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Đơn Hàng Thành Công</h5>
                    <p class="card-text">{{ $totalSuccessfulOrders }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Doanh Thu</h5>
                    <p class="card-text">{{ number_format($totalRevenue, 0, ',', '.') }} đ</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Đơn Hàng Bị Hủy</h5>
                    <p class="card-text">{{ $cancelledOrders }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ: Số Đơn Hàng & Doanh Thu Theo Ngày Trong Tháng -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="header-title mb-3">Biểu Đồ Số Đơn Hàng &amp; Doanh Thu Theo Ngày Trong Tháng</h5>
            <div style="position: relative; height: 300px; width: 70%; margin: 0 auto;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bảng Top 10 Sản Phẩm Bán Chạy Trong Tháng -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="card-widgets">
                <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                <a data-bs-toggle="collapse" href="#top-products-collapse" role="button" aria-expanded="false" aria-controls="top-products-collapse">
                    <i class="ri-subtract-line"></i></a>
                <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
            </div>
            <h5 class="header-title mb-0">TOP 50 Sản Phẩm Bán Chạy Trong Tháng</h5>
            <div id="top-products-collapse" class="collapse pt-3 show">
                @if ($productsSales->isNotEmpty())
                    <ul class="list-group">
                        @foreach ($productsSales as $index => $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $index + 1 }}. {{ $product->product_name }}
                                <span class="badge bg-primary rounded-pill">{{ number_format($product->total_sold) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>Không có dữ liệu bán hàng cho tháng này.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Bảng Top 20 Người Mua Nhiều Nhất Trong Tháng -->
    <div class="card">
        <div class="card-body">
            <div class="card-widgets">
                <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                <a data-bs-toggle="collapse" href="#top-customers-collapse" role="button" aria-expanded="false" aria-controls="top-customers-collapse">
                    <i class="ri-subtract-line"></i></a>
                <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
            </div>
            <h5 class="header-title mb-0">TOP 50 Người Mua Nhiều Nhất Trong Tháng</h5>
            <div id="top-customers-collapse" class="collapse pt-3 show">
                @if ($topCustomers->isNotEmpty())
                    @foreach ($topCustomers as $index => $customer)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $index + 1 }}. {{ $customer->user_name ?? 'Khách vãng lai' }}</h6>
                                <small class="text-muted">
                                    Tổng mua: ${{ number_format($customer->total_purchase, 2) }}
                                </small>
                                @if(isset($customerProductsGrouped[$customer->id_user]))
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#customer-{{ $customer->id_user }}" aria-expanded="false" aria-controls="customer-{{ $customer->id_user }}">
                                        Xem chi tiết
                                    </button>
                                @endif
                            </div>
                            @if(isset($customerProductsGrouped[$customer->id_user]))
                                <div class="collapse mt-2" id="customer-{{ $customer->id_user }}">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Sản Phẩm</th>
                                                <th>Số Lượng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($customerProductsGrouped[$customer->id_user] as $prod)
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
                @else
                    <p class="text-center text-muted">Không có dữ liệu người mua cho tháng này.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Nạp Chart.js từ CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Lấy dữ liệu thống kê từ $dailyStats
    var monthlyData = {!! json_encode($dailyStats) !!};
    var labels = monthlyData.map(function(item) { return item.order_date; });
    var totalOrdersData = monthlyData.map(function(item) { return item.total_orders; });
    var deliveredData = monthlyData.map(function(item) { return item.delivered_orders; });
    var cancelledData = monthlyData.map(function(item) { return item.cancelled_orders; });
    var revenueData = monthlyData.map(function(item) { return item.day_revenue; });

    var ctx = document.getElementById('monthlyChart').getContext('2d');
    var monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Tổng Đơn Hàng',
                    data: totalOrdersData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Đơn Hàng Thành Công',
                    data: deliveredData,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Đơn Hàng Bị Hủy',
                    data: cancelledData,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Doanh Thu (Delivered)',
                    data: revenueData,
                    type: 'line',
                    fill: false,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    borderWidth: 2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Số Đơn' }
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    title: { display: true, text: 'Doanh Thu (đ)' },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
</script>
@endsection

@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
@endpush
