@extends('admin.statistics.dashboard_statistics')
@section('statistics-content')
    <div class="container-fluid">
        <!-- Header & Thông tin tháng -->
        <div class="text-center my-4">
            <h1>Thống Kê Đơn Hàng Theo Tháng</h1>
            <p>
                Từ ngày: <strong>{{ $startOfMonth->format('d/m/Y') }}</strong> đến
                <strong>{{ $endOfMonth->format('d/m/Y') }}</strong>
            </p>
            <p>
                Giá trị trung bình mỗi đơn hàng (AOV): <strong>{{ number_format($averageOrderValue, 0) }}đ</strong>
            </p>
        </div>

        <!-- Form chọn tháng -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form action="{{ route('admin.statistics.monthlyStatistics') }}" method="GET"
                    class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="month" class="form-label">Chọn Tháng (YYYY-MM):</label>
                        <input type="month" id="month" name="month" class="form-control"
                            value="{{ $selectedMonth->format('Y-m') }}" >
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
                        <h3 class="mb-0">{{ number_format($totalRevenue, 0) }}đ</h3>
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
        <div class="card mb-4" style="position: relative;">
            <div class="card-body">
                <h5 class="header-title mb-3">Biểu Đồ Số Đơn Hàng &amp; Doanh Thu Theo Ngày Trong Tháng</h5>
                <!-- Toolbar cho download -->
                <div id="chartToolbar"
                    style="position: absolute; top: 10px; right: 10px; z-index: 10; background: #fff; padding: 5px; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.15);">
                    <button id="downloadPNG" title="Download PNG" class="btn btn-sm btn-light">Download PNG</button>
                    <button id="downloadExcel" title="Download Excel" class="btn btn-sm btn-light">Download Excel</button>
                </div>
                <div style="position: relative; height: 300px; width: 70%; margin: 0 auto;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Row: Biểu đồ Lý Do Bị Hủy & TOP Sản Phẩm Bị Hủy -->
        <div class="row mb-4">
            <!-- Cột trái: Biểu đồ cột Lý Do Bị Hủy -->
            <div class="col-md-6">
                <div class="card mb-4" style="height:350px;">
                    <div class="card-body" style="height:100%; position: relative;">
                        <h5 class="header-title mb-3">Biểu Đồ Lý Do Bị Hủy</h5>
                        <canvas id="cancelReasonsChart" style="height:100%;"></canvas>
                    </div>
                </div>
            </div>
            <!-- Cột phải: TOP 50 Sản Phẩm Bị Hủy -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body" style="max-height:350px; overflow-y:auto;">
                        <h5 class="header-title mb-0">TOP 50 Sản Phẩm Bị Hủy Trong Tháng</h5>
                        @if ($cancelledProducts->isNotEmpty())
                            <ul class="list-group mt-3">
                                @foreach ($cancelledProducts as $index => $product)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $index + 1 }}. {{ $product->product_name }}
                                        <span class="badge bg-primary rounded-pill">
                                            {{ number_format($product->total_cancelled) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-3 text-center text-muted">Không có dữ liệu sản phẩm bị hủy trong tháng này.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng Top 50 Sản Phẩm Bán Chạy Trong Tháng -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-widgets">
                    <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                    <a data-bs-toggle="collapse" href="#top-products-collapse" role="button" aria-expanded="false"
                        aria-controls="top-products-collapse">
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
                                    <span
                                        class="badge bg-primary rounded-pill">{{ number_format($product->total_sold) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>Không có dữ liệu bán hàng cho tháng này.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bảng Top 50 Người Mua Nhiều Nhất Trong Tháng -->
        <div class="card">
            <div class="card-body">
                <div class="card-widgets">
                    <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                    <a data-bs-toggle="collapse" href="#top-customers-collapse" role="button" aria-expanded="false"
                        aria-controls="top-customers-collapse">
                        <i class="ri-subtract-line"></i></a>
                    <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                </div>
                <h5 class="header-title mb-0">TOP 50 Người Mua Nhiều Nhất Trong Tháng</h5>
                <div id="top-customers-collapse" class="collapse pt-3 show">
                    @if ($topCustomers->isNotEmpty())
                        @foreach ($topCustomers as $index => $customer)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $index + 1 }}.
                                        {{ $customer->user_name ?? 'Khách vãng lai' }}</h6>
                                    <small class="text-muted">
                                        Tổng mua: {{ number_format($customer->total_purchase, 2) }}đ
                                    </small>
                                    @if (isset($customerProductsGrouped[$customer->id_user]))
                                        <button class="btn btn-sm btn-outline-primary" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#customer-{{ $customer->id_user }}"
                                            aria-expanded="false" aria-controls="customer-{{ $customer->id_user }}">
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
                    @else
                        <p class="text-center text-muted">Không có dữ liệu người mua cho tháng này.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <!-- Nạp thư viện Chart.js và SheetJS (XLSX) từ CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Biểu đồ: Thống kê Số Đơn & Doanh Thu theo ngày trong tháng
        var monthlyData = {!! json_encode($dailyStats) !!};
        var labels = monthlyData.map(function(item) {
            return item.order_date;
        });
        var totalOrdersData = monthlyData.map(function(item) {
            return item.total_orders;
        });
        var deliveredData = monthlyData.map(function(item) {
            return item.delivered_orders;
        });
        var cancelledData = monthlyData.map(function(item) {
            return item.cancelled_orders;
        });
        var revenueData = monthlyData.map(function(item) {
            return item.day_revenue;
        });

        var ctx = document.getElementById('monthlyChart').getContext('2d');
        var monthlyChart = new Chart(ctx, {
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

        // Sự kiện download PNG cho biểu đồ tháng
        document.getElementById('downloadPNG').addEventListener('click', function() {
            var url = document.getElementById('monthlyChart').toDataURL("image/png");
            var a = document.createElement('a');
            a.href = url;
            a.download = 'monthly_chart.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });

        // Sự kiện download Excel cho biểu đồ tháng
        document.getElementById('downloadExcel').addEventListener('click', function() {
            var ws_data = [
                ["Tổng Doanh Thu Tháng", "{{ number_format($totalRevenue, 0) }} đ"],
                [],
                ["Ngày", "Tổng Đơn Hàng", "Đơn Hàng Thành Công", "Đơn Hàng Bị Hủy", "Doanh Thu (Delivered)"]
            ];
            for (var i = 0; i < monthlyData.length; i++) {
                ws_data.push([
                    monthlyData[i].order_date,
                    monthlyData[i].total_orders,
                    monthlyData[i].delivered_orders,
                    monthlyData[i].cancelled_orders,
                    monthlyData[i].day_revenue
                ]);
            }
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(ws_data);
            ws['!cols'] = [{
                    wch: 20
                },
                {
                    wch: 15
                },
                {
                    wch: 20
                },
                {
                    wch: 20
                },
                {
                    wch: 20
                }
            ];
            XLSX.utils.book_append_sheet(wb, ws, "Chart Data");
            var wbout = XLSX.write(wb, {
                bookType: 'xlsx',
                type: 'array'
            });
            var blob = new Blob([wbout], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'monthly_data.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });

        // Biểu đồ: Lý Do Bị Hủy
        var cancellationLabels = {!! json_encode($canceledReasons->pluck('reason')) !!};
        var cancellationValues = {!! json_encode($canceledReasons->pluck('total')) !!};
        var ctxCancel = document.getElementById('cancelReasonsChart').getContext('2d');
        var cancelReasonsChart = new Chart(ctxCancel, {
            type: 'bar',
            data: {
                labels: cancellationLabels,
                datasets: [{
                    label: 'Số Lượng Đơn Bị Hủy',
                    data: cancellationValues,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số Lượng'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Lý Do'
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    </script>
@endpush
