@extends('admin.statistics.dashboard_statistics')
@section('statistics-content')
    <div class="container-fluid">
        <!-- Header & Thông tin tuần -->
        <div class="text-center my-4">
            <h1>Thống Kê Đơn Hàng Theo Tuần</h1>
            <p>
                Từ ngày: <strong>{{ $startDate->format('d/m/Y') }}</strong> đến
                <strong>{{ $endDate->format('d/m/Y') }}</strong>
            </p>
            <p>
                Giá trị trung bình mỗi đơn hàng (AOV): <strong>${{ number_format($averageOrderValue, 0) }}</strong>
            </p>
        </div>

        <!-- Form chọn ngày -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form action="{{ route('admin.statistics.weeklyStatistics') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Ngày bắt đầu:</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ request('start_date', now()->startOfWeek()->toDateString()) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">Ngày kết thúc:</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ request('end_date', now()->endOfWeek()->toDateString()) }}">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Thống kê</button>
                </form>
            </div>

            <div class="col-md-4 text-end">
                <div class="d-flex justify-content-end align-items-center border-start ps-3">
                    <div class="text-end">
                        <p class="text-muted mb-1">Tổng Doanh Thu Tuần</p>
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

        <!-- Biểu đồ: Số Đơn Hàng & Doanh Thu Theo Ngày Trong Tuần (Chart.js) -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="header-title mb-3">Biểu Đồ Số Đơn Hàng &amp; Doanh Thu Theo Ngày Trong Tuần</h5>
                <div id="chartToolbar"
                    style="position: absolute; top: 10px; right: 10px; z-index: 10; background: #fff; padding: 5px; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.15);">
                    <button id="downloadPNG" title="Download PNG" class="btn btn-sm btn-light">Download PNG</button>
                    <button id="downloadExcel" title="Download Excel" class="btn btn-sm btn-light">Download Excel</button>
                </div>
                <div id="chartToolbarContainer"
                    style="position: relative; width: 70%; margin: 0 auto; height: 300px; max-height: 300px;">

                    <!-- Canvas cho Chart.js -->
                    <canvas id="weeklyChart" style="margin-top: 40px; height: 100%;"></canvas>
                </div>

            </div>
        </div>
        <!-- Bảng Top 20 Sản Phẩm Bán Chạy Trong Tuần -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-widgets">
                    <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                    <a data-bs-toggle="collapse" href="#top-products-collapse" role="button" aria-expanded="false"
                        aria-controls="top-products-collapse">
                        <i class="ri-subtract-line"></i></a>
                    <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                </div>
                <h5 class="header-title mb-0">TOP 20 Sản Phẩm Bán Chạy Trong Tuần</h5>
                <div id="top-products-collapse" class="collapse pt-3 show">
                    @if ($productsSales->isNotEmpty())
                        <ul class="list-group">
                            @foreach ($productsSales as $index => $product)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $index + 1 }}. {{ $product->product_name }}
                                    <span
                                        class="badge bg-primary rounded-pill">{{ number_format($product->total_sold) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-muted">Không có dữ liệu bán hàng cho tuần này.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bảng Top 20 Người Mua Nhiều Nhất Trong Tuần -->
        <div class="card">
            <div class="card-body">
                <div class="card-widgets">
                    <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                    <a data-bs-toggle="collapse" href="#top-customers-collapse" role="button" aria-expanded="false"
                        aria-controls="top-customers-collapse">
                        <i class="ri-subtract-line"></i></a>
                    <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                </div>
                <h5 class="header-title mb-3">TOP 20 Người Mua Nhiều Nhất Trong Tuần</h5>
                <div id="top-customers-collapse" class="collapse show">
                    @if ($topCustomers->isNotEmpty())
                        <div class="list-group">
                            @foreach ($topCustomers as $index => $customer)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">
                                                {{ $index + 1 }}. {{ $customer->user_name ?? 'Khách vãng lai' }}
                                            </h5>
                                            <small class="text-muted">
                                                Tổng mua: ${{ number_format($customer->total_purchase, 0) }}
                                            </small>
                                        </div>
                                        @if (isset($customerProductsGrouped[$customer->id_user]))
                                            <button class="btn btn-sm btn-outline-primary" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#customer-{{ $customer->id_user }}" aria-expanded="false"
                                                aria-controls="customer-{{ $customer->id_user }}">
                                                Xem chi tiết
                                            </button>
                                        @endif
                                    </div>
                                    @if (isset($customerProductsGrouped[$customer->id_user]))
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
                        </div>
                    @else
                        <p class="text-center text-muted">Không có dữ liệu người mua cho tuần này.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Nạp Chart.js từ CDN cho biểu đồ Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Lấy dữ liệu thống kê từ controller (đảm bảo có dữ liệu 7 ngày)
        // Dữ liệu thống kê từ controller
        var weeklyData = {!! json_encode($dailyStats) !!};
        var labels = weeklyData.map(function(item) {
            return item.order_date;
        });
        var totalOrdersData = weeklyData.map(function(item) {
            return item.total_orders;
        });
        var deliveredData = weeklyData.map(function(item) {
            return item.delivered_orders;
        });
        var cancelledData = weeklyData.map(function(item) {
            return item.cancelled_orders;
        });
        var revenueData = weeklyData.map(function(item) {
            return item.day_revenue;
        });

        // Cấu hình biểu đồ Chart.js
        var ctx = document.getElementById('weeklyChart').getContext('2d');
        var weeklyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
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
                        title: {
                            display: true,
                            text: 'Số Đơn'
                        }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Doanh Thu (đ)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
        document.getElementById('downloadPNG').addEventListener('click', function() {
            // Lấy data URL của canvas (mã hóa hình ảnh PNG)
            var url = document.getElementById('weeklyChart').toDataURL("image/png");

            // Tạo phần tử <a> tạm thời và kích hoạt download
            var a = document.createElement('a');
            a.href = url;
            a.download = 'weekly_chart.png'; // Tên file tải về
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
        document.getElementById('downloadExcel').addEventListener('click', function() {
    // Hàng dữ liệu: dòng tóm tắt tổng doanh thu, dòng trống và tiêu đề bảng
    var ws_data = [
        ["Tổng Doanh Thu Tuần", "{{ number_format($totalRevenue, 0) }} đ"],
        [], // Dòng trống để phân cách
        ["Ngày", "Tổng Đơn Hàng", "Đơn Hàng Thành Công", "Đơn Hàng Bị Hủy", "Doanh Thu (Delivered)"]
    ];

    // Thêm dữ liệu chi tiết theo từng ngày
    for (var i = 0; i < weeklyData.length; i++) {
        ws_data.push([
            weeklyData[i].order_date,
            weeklyData[i].total_orders,
            weeklyData[i].delivered_orders,
            weeklyData[i].cancelled_orders,
            weeklyData[i].day_revenue
        ]);
    }

    // Tạo workbook mới và chuyển dữ liệu thành worksheet
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.aoa_to_sheet(ws_data);

    // Đặt chiều rộng cột (wch: width character) để file Excel trông đẹp hơn
    ws['!cols'] = [
        { wch: 20 }, // Cột "Ngày"
        { wch: 15 }, // Cột "Tổng Đơn Hàng"
        { wch: 20 }, // Cột "Đơn Hàng Thành Công"
        { wch: 20 }, // Cột "Đơn Hàng Bị Hủy"
        { wch: 20 }  // Cột "Doanh Thu (Delivered)"
    ];

    // Gắn worksheet vào workbook
    XLSX.utils.book_append_sheet(wb, ws, "Chart Data");

    // Xuất workbook thành định dạng array buffer
    var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
    var blob = new Blob([wbout], {
        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    });

    // Tạo URL từ Blob và kích hoạt download
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'chart_data.xlsx';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
});

    </script>

@endsection

@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
@endpush
