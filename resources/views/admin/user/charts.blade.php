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
                        <h6 class="text-uppercase mt-0" title="Customers">Tài khoản mới</h6>
                        <h2 class="my-2">{{ $currentMonthCount }}</h2>
                        <p class="mb-0">
                            <span class="badge bg-white bg-opacity-10 me-1">{{ $growthRate }}%</span>
                            <span class="text-nowrap">So với tháng trước</span>
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
                            <span class="badge bg-white bg-opacity-10 me-1"></span>
                            <span class="text-nowrap">Since last month</span>
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
                        <h6 class="text-uppercase mt-0" title="Customers">Đã đặt hàng</h6>
                        <h2 class="my-2">{{ $activeUsers }}</h2>
                        <p class="mb-0">
                            <span class="badge bg-white bg-opacity-25 me-1">{{ $activePercentage }}%</span>
                            <span class="text-nowrap">So với tổng tài khoản</span>
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
                                    <div class="col-md-6">
                                        <h4>Phần trăm tăng trưởng</h4>
                                        <p>{{ number_format($growthRate, 2) }}%</p>
                                    </div>
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
                                    <a class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill"
                                        href="#v-pills-settings" role="tab" aria-controls="v-pills-settings"
                                        aria-selected="false">
                                        Tiềm năng <span
                                            class="badge bg-info-subtle text-info">{{ $totalUsersWithMultipleOrders }}</span>
                                    </a>
                                </div>
                            </div> <!-- end col-->

                            <div class="col-sm-9">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade active show" id="v-pills-home" role="tabpanel"
                                        aria-labelledby="v-pills-home-tab">
                                        <div class="card-body">
                                            <table id="fixed-header-datatable"
                                                class="table table-striped dt-responsive nowrap table-striped  w-100">
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
                                        </div> <!-- end card body-->
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                        aria-labelledby="v-pills-profile-tab">
                                        <table id="fixed-header-datatable"
                                            class="table table-striped dt-responsive nowrap table-striped  w-100">
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
                                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                                        aria-labelledby="v-pills-settings-tab">
                                        <table id="fixed-header-datatable"
                                            class="table table-striped dt-responsive nowrap table-striped  w-100">
                                            <thead>
                                                <tr>
                                                    <th>Tên</th>
                                                    <th>Email</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($usersWithMultipleOrders as $value)
                                                    <tr>
                                                        <td>{{ $value->name }}</td>
                                                        <td>{{ $value->email }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div> <!-- end tab-content-->
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
                    <div class="card-header">
                        <h4 class="header-title">Thống kê số tài khoản mới</h4>

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="datePicker">Chọn ngày:</label>
                                            <input type="date" id="datePicker" class="form-control"
                                                value="{{ now()->toDateString() }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Theo ngày</h5>
                                            <div id="dailyChart"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Theo tuần</h5>
                                            <div id="weeklyChart"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Theo tháng</h5>
                                            <div id="monthlyChart"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Theo năm</h5>
                                            <div id="yearlyChart"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row-->
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Top 5 Người Dùng Chi Tiêu Nhiều Nhất</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <table id="fixed-header-datatable"
                                class="table table-striped dt-responsive nowrap table-striped  w-100">
                                <thead>
                                    <tr>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Tổng Tiền Chi Tiêu</th>
                                    </tr>
                                </thead>
                                <tbody id="topSpendersList">

                                </tbody>
                            </table>

                        </div>
                        <!-- end row-->
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->

        </div>

        <div class="row">

        </div>

    </div>
@endsection

@push('styles')
    <x-admin.data-table-styles />
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
        $(document).ready(function() {
            function loadCharts(date) {
                $.ajax({
                    url: '/admin/users/getUserStats',
                    method: 'GET',
                    data: {
                        date: date
                    },
                    success: function(response) {
                        updateChart("dailyChart", response.dailyUsers, "Tài khoản mới theo ngày");
                        updateChart("weeklyChart", response.weeklyUsers, "Tài khoản mới theo tuần");
                        updateChart("monthlyChart", response.monthlyUsers, "Tài khoản mới theo tháng");
                        updateChart("yearlyChart", response.yearlyUsers, "Tài khoản mới theo năm");
                    }
                });
            }

            function updateChart(elementId, data, title) {
                let categories = Object.keys(data);
                let values = Object.values(data);

                var options = {
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    series: [{
                        name: title,
                        data: values
                    }],
                    xaxis: {
                        categories: categories
                    },
                    colors: ['#007bff']
                };
                new ApexCharts(document.querySelector("#" + elementId), options).render();
            }

            // Load dữ liệu ban đầu
            let defaultDate = $('#datePicker').val();
            loadCharts(defaultDate);

            // Cập nhật khi chọn ngày khác
            $('#datePicker').on('change', function() {
                let selectedDate = $(this).val();
                loadCharts(selectedDate);
            });
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
