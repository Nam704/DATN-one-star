@extends('client.layouts.home.layout')

@section('title', 'Thống kê của bạn')

@section('content')
<div class="container py-4">

    <!-- Tiêu đề -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Thống kê của bạn</h2>
        <span class="text-muted">Xin chào, {{ $user->name }}</span>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-primary h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="ri-wallet-2-line display-6 me-3"></i>
                    <div>
                        <div class="small">Tổng chi tiêu</div>
                        <h5 class="mb-0">{{ number_format($totalSpent, 0, ',', '.') }} VNĐ</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-info h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="ri-shopping-basket-line display-6 me-3"></i>
                    <div>
                        <div class="small">Tổng đơn hàng</div>
                        <h5 class="mb-0">{{ $totalOrders }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-success h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="ri-shopping-cart-line display-6 me-3"></i>
                    <div>
                        <div class="small">Sản phẩm đã mua</div>
                        <h5 class="mb-0">{{ $totalProductsBought }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            @php
                $rankClass = match($customerRank) {
                    'VIP' => 'bg-danger',
                    'Kim Cương' => 'bg-light text-dark',
                    'Vàng' => 'bg-warning',
                    'Bạc' => 'bg-secondary',
                    default => 'bg-dark'
                };
                $rankIcon = match($customerRank) {
                    'VIP' => 'mdi mdi-crown',
                    'Kim Cương' => 'mdi mdi-gem',
                    'Vàng' => 'mdi mdi-gold',
                    'Bạc' => 'mdi mdi-silverware',
                    default => 'mdi mdi-account'
                };
            @endphp
            <div class="card border-0 shadow-sm text-white {{ $rankClass }} h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="{{ $rankIcon }} display-6 me-3"></i>
                    <div>
                        <div class="small">Hạng khách hàng</div>
                        <h5 class="mb-0">{{ $customerRank }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ trạng thái đơn hàng -->
    <div class="row mt-5">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Biểu đồ đơn hàng hủy hoàn thành và hoàn hàng</div>
                <div class="card-body d-flex justify-content-center">
                <canvas id="orderStatusChart" style="max-width: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Chi tiết đơn hàng -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Đơn hàng lớn nhỏ và gần nhất</div>
                <div class="card-body">
                    @if($maxOrder)
                        <p><strong>Đơn lớn nhất:</strong><a href="{{ route('client.orders.detail', $maxOrder->id) }}" class="text-decoration-none text-primary"> #{{ $maxOrder->code }}  - {{ number_format($maxOrder->total, 0, ',', '.') }} VNĐ</p></a>
                    @endif
                    @if($minOrder)
                        <p><strong>Đơn nhỏ nhất:</strong><a href="{{ route('client.orders.detail', $minOrder->id) }}" class="text-decoration-none text-primary"> #{{ $minOrder->code }} - {{ number_format($minOrder->total, 0, ',', '.') }} VNĐ</p></a>
                    @endif
                    @if($latestOrder)
                        <p><strong>Đơn gần nhất:</strong><a href="{{ route('client.orders.detail', $latestOrder->id) }}" class="text-decoration-none text-primary"> #{{ $latestOrder->code }} - {{ number_format($latestOrder->total, 0, ',', '.') }} VNĐ</p></a>
                        <p><small class="text-muted">Ngày tạo: {{ $latestOrder->created_at->format('d/m/Y H:i') }}</small></p>
                    @else
                        <p>Không có đơn hàng nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top sản phẩm -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Top 10 sản phẩm bạn mua nhiều nhất</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Ảnh</th>
                                    <th>Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPurchasedProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td><img src="{{ asset($product->image_primary) }}" width="50" alt=""></td>
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
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetch("{{ route('client.statistics.getOrderStatusStats') }}")
            .then(response => {
                if (!response.ok) throw new Error("Lỗi khi fetch dữ liệu");
                return response.json();
            })
            .then(data => {
                const ctx = document.getElementById("orderStatusChart").getContext("2d");

                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: ["Đã giao", "Hoàn hàng", "Đã hủy"],
                        datasets: [{
                            data: [
                                data.delivered_orders,
                                data.returned_orders,
                                data.cancelled_orders
                            ],
                            backgroundColor: ["#28a745", "#ffc107", "#dc3545"]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: "bottom",
                                labels: {
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ": " + context.raw + " đơn";
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error("Lỗi khi tải dữ liệu biểu đồ:", error);
            });
    });
</script>
@endsection

