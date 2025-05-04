@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-dark">
                            <i class="mdi mdi-arrow-left-thin"></i>
                            Back
                        </a>
                    </div>
                    <h4 class="page-title">Thống kê tài khoản</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xxl-4 col-sm-6">
                <div class="card widget-flat text-bg-pink">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-account-multiple-plus widget-icon"></i>
                        </div>
                        <h6 class="text-uppercase mt-0" title="Customers">Tổng tài khoản mới(trong tháng)</h6>
                        <h2 class="my-2">{{ $currentMonthCount }}</h2>
                        <p class="mb-0">
                        </p>
                    </div>
                </div>
            </div> <!-- end col-->

            <div class="col-xxl-4 col-sm-6">
                <div class="card widget-flat text-bg-purple">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-account-group widget-icon"></i>
                        </div>
                        <h6 class="text-uppercase mt-0" title="Customers">Tổng tài khoản</h6>
                        <h2 class="my-2">{{ $totalUsers }}</h2>
                        <p class="mb-0">
                        </p>
                    </div>
                </div>
            </div> <!-- end col-->

            <div class="col-xxl-4 col-sm-6">
                <div class="card widget-flat text-bg-info">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-wifi widget-icon"></i>
                        </div>
                        <h6 class="text-uppercase mt-0" title="Customers">Tổng tài khoản đã đặt hàng</h6>
                        <h2 class="my-2">{{ $totalUsersWithOrders }}</h2>
                        <p class="mb-0">
                        </p>
                    </div>
                </div>
            </div> <!-- end col-->
        </div>

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Thống kê trạng thái tài khoản</h4>

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>Tài khoản hoạt động</h4>
                                        <p>{{ $activeUsers }} / {{ $totalUsers }}
                                            ({{ number_format($activePercentage, 2) }}%)</p>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <h4>Phần trăm tăng trưởng</h4>
                                        <p>{{ number_format($growthRate, 2) }}%</p>
                                    </div> --}}
                                </div>

                                <div id="userStatusChart"></div>
                            </div>
                        </div>
                        <!-- end row-->
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Thống kê tài khoản đặt hàng</h4>

                    </div>
                    <div class="card-body" style="margin-top: -20px">
                        <div class="row">
                            <div class="col-sm-3 mb-2 mb-sm-0">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">
                                    <a class="nav-link active show" id="v-pills-home-tab" data-bs-toggle="pill"
                                        href="#v-pills-home" role="tab" aria-controls="v-pills-home"
                                        aria-selected="true">
                                        Đã mua hàng <span
                                            class="badge bg-info-subtle text-info">{{ $totalUsersWithOrders }}</span>
                                    </a>
                                    <a class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill"
                                        href="#v-pills-profile" role="tab" aria-controls="v-pills-profile"
                                        aria-selected="false">
                                        Chưa mua hàng <span
                                            class="badge bg-info-subtle text-info">{{ $totalUsersWithoutOrders }}</span>
                                    </a>
                                    {{-- <a class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill"
                                        href="#v-pills-settings" role="tab" aria-controls="v-pills-settings"
                                        aria-selected="false">
                                        Tiềm năng <span
                                            class="badge bg-info-subtle text-info">{{ $totalUsersWithMultipleOrders }}</span>
                                    </a> --}}
                                </div>
                            </div> <!-- end col-->

                            <div class="col-sm-9">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade active show" id="v-pills-home" role="tabpanel"
                                        aria-labelledby="v-pills-home-tab">
                                        <div class="scrollable-table">
                                            <table class="table table-striped dt-responsive nowrap w-100 mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Tên</th>
                                                        <th>Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($usersWithOrders as $value)
                                                        <tr>
                                                            <td>{{ $value->name }}</td>
                                                            <td>{{ $value->email }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                        aria-labelledby="v-pills-profile-tab">
                                        <div class="scrollable-table">
                                            <table class="table table-striped dt-responsive nowrap w-100 mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Tên</th>
                                                        <th>Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($usersWithoutOrders as $value)
                                                        <tr>
                                                            <td>{{ $value->name }}</td>
                                                            <td>{{ $value->email }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- end tab-content-->
                            </div> <!-- end col-->
                        </div>
                        <!-- end row-->
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Thống kê tài khoản theo khu vực</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <canvas id="userLocationChart"></canvas>
                        </div>
                        <!-- end row-->
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="header-title mb-0">Thống kê số tài khoản mới</h4>
                    </div>
                    <div class="card-body">
                        {{-- 1. Các input filter, chỉ hiện container tương ứng --}}

                        <div id="filterInputs" class="mb-3">
                            <div data-timeframe="daily" class="filter-group">
                                <label for="dailyDate">Chọn ngày:</label>
                                <input type="date" id="dailyDate" class="form-control"
                                    value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}">
                            </div>
                            <div data-timeframe="weekly" class="filter-group d-none">
                                <label>Chọn khoảng:</label>
                                <div class="d-flex gap-2">
                                    <input type="date" id="weekStart" class="form-control"
                                        value="{{ now()->subWeek()->startOfWeek()->toDateString() }}"
                                        max="{{ now()->toDateString() }}">
                                    <span class="align-self-center">→</span>
                                    <input type="date" id="weekEnd" class="form-control"
                                        value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}">
                                </div>
                            </div>
                            <div data-timeframe="monthly" class="filter-group d-none">
                                <label for="monthPicker">Chọn tháng:</label>
                                <input type="month" id="monthPicker" class="form-control"
                                    value="{{ now()->format('Y-m') }}" max="{{ now()->format('Y-m') }}">
                            </div>
                            <div data-timeframe="yearly" class="filter-group d-none">
                                <label for="yearPicker">Chọn năm:</label>
                                <input type="number" id="yearPicker" class="form-control" min="2000"
                                    max="{{ now()->year }}" step="1" value="{{ now()->year }}">
                            </div>
                            <button id="applyFilter" class="btn btn-primary mt-2">Lọc</button>
                        </div>
                        {{-- 2. Nav‑pills chuyển tab --}}
                        <ul class="nav nav-pills mb-3" id="timeframeTabs" role="tablist">
                            @foreach (['daily' => 'Theo ngày', 'weekly' => 'Theo tuần', 'monthly' => 'Theo tháng', 'yearly' => 'Theo năm'] as $tf => $label)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link @if ($tf === 'daily') active @endif"
                                        data-bs-toggle="pill" data-timeframe="{{ $tf }}" type="button">
                                        {{ $label }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        {{-- 3. Tab content --}}
                        <div class="tab-content">
                            <div class="tab-pane active show" id="dailyTab">
                                <div id="dailyChart"></div>
                            </div>
                            <div class="tab-pane" id="weeklyTab">
                                <div id="weeklyChart"></div>
                            </div>
                            <div class="tab-pane" id="monthlyTab">
                                <div id="monthlyChart"></div>
                            </div>
                            <div class="tab-pane" id="yearlyTab">
                                <div id="yearlyChart"></div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Top 5 Người Dùng Chi Tiêu Nhiều Nhất</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <table id="fixed-header-datatable"
                                class="table table-striped dt-responsive nowrap table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Tổng Tiền Chi Tiêu</th>
                                    </tr>
                                </thead>
                                <tbody id="topSpendersList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end card-->
    </div> <!-- end col -->

    </div>

    </div>
@endsection

@push('styles')
    <style>
        .scrollable-table {
            max-height: 260px;
            /* ~5 dòng x ~52px/dòng */
            overflow-y: auto;
        }
    </style>
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                chart: {
                    type: 'pie',
                    height: 350
                },
                series: [{{ $activeUsers }}, {{ $inactiveUsers }}],
                labels: ["Đang hoạt động", "Ngừng hoạt động"],
                colors: ['#28a745', '#dc3545']
            };

            var chart = new ApexCharts(document.querySelector("#userStatusChart"), options);
            chart.render();
        });
    </script>

    <script>
        $(function() {
            function showFilterGroup(tf) {
                $('#filterInputs .filter-group').addClass('d-none');
                $('#filterInputs [data-timeframe="' + tf + '"]').removeClass('d-none');
            }

            function getFilterParams() {
                let activeBtn = $('#timeframeTabs .active');
                let tf = activeBtn.data('timeframe');
                let params = {
                    timeframe: tf
                };
                switch (tf) {
                    case 'daily':
                        params.date = $('#dailyDate').val();
                        break;
                    case 'weekly':
                        params.start_date = $('#weekStart').val();
                        params.end_date = $('#weekEnd').val();
                        break;
                    case 'monthly':
                        params.month = $('#monthPicker').val();
                        break;
                    case 'yearly':
                        params.year = $('#yearPicker').val();
                        break;
                }
                return params;
            }

            function loadChart() {
                let params = getFilterParams();
                $.get('/admin/users/getUserStats', params)
                    .done(function(res) {
                        switch (params.timeframe) {
                            case 'daily':
                                $('#dailyChart').empty();
                                new ApexCharts(document.querySelector("#dailyChart"), {
                                    chart: {
                                        type: 'bar',
                                        height: 300
                                    },
                                    series: [{
                                        name: 'Số TK mới',
                                        data: [res.count]
                                    }],
                                    xaxis: {
                                        categories: [params.date]
                                    }
                                }).render();
                                break;
                            case 'weekly':
                                $('#weeklyChart').empty();
                                new ApexCharts(document.querySelector("#weeklyChart"), {
                                    chart: {
                                        type: 'bar',
                                        height: 300
                                    },
                                    series: [{
                                        name: 'Số TK mới',
                                        data: Object.values(res.daily)
                                    }],
                                    xaxis: {
                                        categories: Object.keys(res.daily)
                                    }
                                }).render();
                                break;
                            case 'monthly':
                                $('#monthlyChart').empty();
                                new ApexCharts(document.querySelector("#monthlyChart"), {
                                    chart: {
                                        type: 'bar',
                                        height: 300
                                    },
                                    series: [{
                                        name: 'Số TK mới',
                                        data: Object.values(res.daily)
                                    }],
                                    xaxis: {
                                        categories: Object.keys(res.daily)
                                    }
                                }).render();
                                break;
                            case 'yearly':
                                $('#yearlyChart').empty();
                                new ApexCharts(document.querySelector("#yearlyChart"), {
                                    chart: {
                                        type: 'bar',
                                        height: 300
                                    },
                                    series: [{
                                        name: 'Số TK mới',
                                        data: Object.values(res.monthly)
                                    }],
                                    xaxis: {
                                        categories: Object.keys(res.monthly)
                                    }
                                }).render();
                                break;
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error('Error loading chart:', textStatus, errorThrown);
                    });
            }

            $('#timeframeTabs button').click(function() {
                $('#timeframeTabs button').removeClass('active');
                $(this).addClass('active');
                let tf = $(this).data('timeframe');
                showFilterGroup(tf);
                $('.tab-pane').removeClass('show active');
                $('#' + tf + 'Tab').addClass('show active');
                loadChart();
            });

            $('#applyFilter').click(function() {
                let tf = $('#timeframeTabs .active').data('timeframe');
                let isValid = true;
                let errorMsg = '';

                // Lấy tháng và năm hiện tại
                let currentDate = new Date();
                let currentYear = currentDate.getFullYear();
                let currentMonth = currentDate.getMonth() + 1; // Tháng bắt đầu từ 0, cộng thêm 1

                switch (tf) {
                    case 'daily':
                        let dailyDate = $('#dailyDate').val();
                        if (!dailyDate) {
                            isValid = false;
                            errorMsg = 'Vui lòng chọn ngày.';
                        }
                        break;
                    case 'weekly':
                        let weekStart = $('#weekStart').val();
                        let weekEnd = $('#weekEnd').val();
                        if (!weekStart || !weekEnd) {
                            isValid = false;
                            errorMsg = 'Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc.';
                        } else if (new Date(weekStart) > new Date(weekEnd)) {
                            isValid = false;
                            errorMsg = 'Ngày bắt đầu không thể sau ngày kết thúc.';
                        }
                        break;
                    case 'monthly':
                        let monthPicker = $('#monthPicker').val();
                        if (!monthPicker) {
                            isValid = false;
                            errorMsg = 'Vui lòng chọn tháng.';
                        } else {
                            let [selectedYear, selectedMonth] = monthPicker.split('-').map(Number);
                            if (selectedYear > currentYear || (selectedYear === currentYear &&
                                    selectedMonth > currentMonth)) {
                                isValid = false;
                                errorMsg = 'Không thể chọn tháng trong tương lai.';
                            }
                        }
                        break;
                    case 'yearly':
                        let yearPicker = $('#yearPicker').val();
                        if (!yearPicker || isNaN(yearPicker) || yearPicker < 2000 || yearPicker >
                            currentYear) {
                            isValid = false;
                            errorMsg = 'Vui lòng nhập năm hợp lệ (từ 2000 đến ' + currentYear + ').';
                        }
                        break;
                }

                if (!isValid) {
                    alert(errorMsg);
                    return;
                }

                // Gọi hàm loadChart() để xử lý dữ liệu sau khi validate thành công
                loadChart();
            });

            let initialTimeframe = $('#timeframeTabs .active').data('timeframe');
            showFilterGroup(initialTimeframe);
            loadChart();
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch("{{ route('admin.users.locationStats') }}")
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById("userLocationChart").getContext("2d");
                    new Chart(ctx, {
                        type: "pie",
                        data: {
                            labels: Object.keys(data),
                            datasets: [{
                                label: "Số lượng tài khoản",
                                data: Object.values(data),
                                backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0",
                                    "#9966FF"
                                ],
                            }]
                        }
                    });
                });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch("{{ route('admin.users.topSpenders') }}")
                .then(response => response.json())
                .then(users => {
                    let html = "";
                    users.forEach(order => {
                        html += `<tr>
                            <td>${order.user?.name || "N/A"}</td>
                            <td>${order.user?.email || "N/A"}</td>
                            <td>${order.total_spent.toLocaleString("vi-VN")} VND</td>
                        </tr>`;
                    });
                    document.getElementById("topSpendersList").innerHTML = html;
                });
        });
    </script>
@endpush
