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
            <div class="col-lg-6">
                <div class="card">
                    <canvas id="orderStatusChart"></canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var userId = {{ $user->id }}; // Lấy ID user từ blade template
        console.log('userId');
        fetch("{{ route('admin.users.getOrderStatusStats', '') }}/" + userId)
            .then(response => response.json())
            .then(data => {
                // Kiểm tra xem dữ liệu trả về từ API có hợp lệ không
                console.log(data); // Log ra để kiểm tra dữ liệu

                var ctx = document.getElementById("orderStatusChart").getContext("2d");

                // Tạo biểu đồ pie với dữ liệu
                new Chart(ctx, {
                    type: "pie", // Biểu đồ hình tròn
                    data: {
                        labels: ["Đã nhận hàng", "Hoàn hàng", "Hủy đơn hàng"], // Các trạng thái đơn hàng
                        datasets: [{
                            data: [
                                data.delivered_orders,    // Đơn đã nhận hàng
                                data.returned_orders,     // Đơn hoàn hàng
                                data.cancelled_orders     // Đơn hủy
                            ], 
                            backgroundColor: ["#36A2EB", "#FFCE56", "#FF6384"] // Màu sắc cho từng trạng thái
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: "top"
                            }
                        }
                    }
                });
            })
            .catch(error => console.error("Lỗi khi lấy dữ liệu:", error));
    });
</script>
@endpush
