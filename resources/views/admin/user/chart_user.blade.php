@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid">

    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-dark">
                        <i class="mdi mdi-arrow-left-thin"></i> Back
                    </a>
                </div>
                <h4 class="page-title">Thống kê tài khoản: {{ $user->name }}</h4>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row">
        <!-- Tổng chi tiêu -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-purple">
                <div class="card-body d-flex align-items-center">
                    <i class="ri-wallet-2-line widget-icon ms-3"></i>
                    <div class="ms-3">
                        <h6 class="text-uppercase mt-0">Tổng chi tiêu</h6>
                        <h2 class="my-2">{{ number_format($totalSpent, 0, ',', '.') }} VNĐ</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tổng số đơn -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-info">
                <div class="card-body d-flex align-items-center">
                    <i class="ri-shopping-basket-line widget-icon ms-3"></i>
                    <div class="ms-3">
                        <h6 class="text-uppercase mt-0">Tổng đơn hàng</h6>
                        <h2 class="my-2">{{ $totalOrders }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tổng sản phẩm đã mua -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-success">
                <div class="card-body d-flex align-items-center">
                    <div class="float-start">
                        <i class="ri-shopping-cart-line widget-icon"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-uppercase mt-0" title="Sản phẩm đã mua">Sản phẩm đã mua</h6>
                        <h2 class="my-2">{{ $totalProductsBought }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hạng khách hàng -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat 
        @if ($customerRank === 'VIP') text-bg-danger 
        @elseif ($customerRank === 'Kim Cương') text-bg-light text-dark 
        @elseif ($customerRank === 'Vàng') text-bg-warning 
        @elseif ($customerRank === 'Bạc') text-bg-info 
        @else text-bg-secondary 
        @endif">
                <div class="card-body d-flex align-items-center">
                    <div class="float-start" style="padding-left: 20px">
                        @if ($customerRank === 'VIP')
                        <i class="mdi mdi-crown widget-icon text-light"></i>
                        @elseif ($customerRank === 'Kim Cương')
                        <i class="mdi mdi-gem widget-icon text-light"></i>
                        @elseif ($customerRank === 'Vàng')
                        <i class="mdi mdi-gold widget-icon text-light"></i>
                        @elseif ($customerRank === 'Bạc')
                        <i class="mdi mdi-silverware widget-icon text-light"></i> <!-- Thay bằng icon này -->
                        @else
                        <i class="mdi mdi-account widget-icon text-light"></i>
                        @endif
                    </div>
                    <div class="ms-3">
                        <h6 class="text-uppercase mt-0">Hạng Khách Hàng</h6>
                        <h2 class="my-2">{{ $customerRank }}</h2>
                    </div>
                </div>
            </div>
        </div>




    </div>

    <!-- Chart + Order Info -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title mb-0 mb-1">Biểu đồ đơn hàng hủy hoàn thành và hoàn hàng</h5>
                    <div class="card p-3 d-flex align-items-center">
                        <canvas id="orderStatusChart" style="max-width: 300px; max-height: 300px;"></canvas>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>

        <!-- Chi tiết đơn hàng + Đơn hàng gần nhất -->
        <div class="col-lg-6">
            <div class="card p-3">
                <div class="card-body">
                    <h5 class="card-title">Đơn hàng lớn bé và gần nhất</h5>
                    <p class="card-text">
                        @if($maxOrder)
                        <strong>Đơn lớn nhất:</strong>
                        <a href="{{ route('admin.orders.detail', $maxOrder->id) }}">
                            #{{ $maxOrder->code }} - {{ number_format($maxOrder->total, 0, ',', '.') }} VNĐ
                        </a>
                        <br>

                        @if($minOrder)
                        <strong>Đơn nhỏ nhất:</strong>
                        <a href="{{ route('admin.orders.detail', $minOrder->id) }}">
                            #{{ $minOrder->code }} - {{ number_format($minOrder->total, 0, ',', '.') }} VNĐ
                        </a>
                        <br>
                        @endif
                        @else
                        Không có dữ liệu đơn hàng.
                        @endif

                        @if($latestOrder)
                        <strong>Đơn hàng gần nhất</strong>
                        <a href="{{ route('admin.orders.detail', $latestOrder->id) }}">
                            #{{ $latestOrder->code }} - {{ number_format($latestOrder->total, 0, ',', '.') }} VNĐ
                        </a>
                        <strong>Ngày tạo:</strong> {{ $latestOrder->created_at->format('d/m/Y H:i') }}
                    </p>
                    @else
                    Không có đơn hàng nào.
                    @endif
                    </p>
                </div>
            </div>
        </div>
    </div>


    <!-- Top 10 sản phẩm -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top 10 sản phẩm {{ $user->name }} mua nhiều nhất</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tên Sản Phẩm</th>
                                <th>Hình Ảnh</th>
                                <th>Số Lượng Mua</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPurchasedProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>
                                    <img src="{{ asset($product->image_primary) }}" alt="{{ $product->name }}" width="50">
                                </td>
                                <td>{{ $product->total_quantity }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">Không có dữ liệu.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

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
        fetch("{{ route('admin.users.getOrderStatusStats', $user->id) }}")
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById("orderStatusChart").getContext("2d");
                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: ["Đã nhận hàng", "Hoàn hàng", "Hủy đơn hàng"],
                        datasets: [{
                            data: [
                                data.delivered_orders,
                                data.returned_orders,
                                data.cancelled_orders
                            ],
                            backgroundColor: ["#36A2EB", "#FFCE56", "#FF6384"]
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
            .catch(error => console.error("Lỗi khi lấy dữ liệu biểu đồ:", error));
    });
</script>
@endpush