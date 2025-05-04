@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="tab-pane fade show active" id="orders">
                        <div class="card-header">
                            <h3>Đơn hàng > Danh sách</h3>

                            <!-- Hiển thị thông báo lỗi nếu có -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Summary Metrics -->
                            <div class="summary row mb-1">
                                <div class="col-md-3">
                                    <div class="widget-flat text-bg-info">
                                        <div class="card-body">
                                            <h5>Tổng số đơn hàng</h5>
                                            <p>{{ $totalOrders }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card widget-flat text-bg-info">
                                        <div class="card-body">
                                            <h5>Đơn hàng đang mở</h5>
                                            <p>{{ $openOrders }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card widget-flat text-bg-info">
                                        <div class="card-body">
                                            <h5>Giá trung bình</h5>
                                            <p>{{ number_format($averagePrice, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card widget-flat text-bg-info">
                                        <div class="card-body">
                                            <h5>Tổng doanh thu</h5>
                                            <p>{{ number_format($totalRevenue, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filter and Search -->
                            <div class="filters mb-4">
                                <form method="GET" action="{{ route('admin.orders.list') }}"
                                    class="row g-3 align-items-end">
                                    <!-- Status Tabs -->
                                    <div class="col-12">
                                        <ul class="nav nav-tabs">
                                            <li class="nav-item">
                                                <a class="nav-link {{ request('group_status') == 'All' || !request('group_status') ? 'active' : '' }}"
                                                    href="?group_status=All">Tất cả</a>
                                            </li>
                                            @foreach ($groupStatuses as $groupStatus)
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request('group_status') == $groupStatus ? 'active' : '' }}"
                                                        href="?group_status={{ $groupStatus }}">{{ $groupStatus }}
                                                        ({{ $groupStatusCounts[$groupStatus] ?? 0 }})
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Tìm kiếm theo mã" value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#filterModal">
                                            <i class="mdi mdi-filter-menu fs-5"></i> Bộ lọc nâng cao
                                        </button>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Orders Table -->
                            <div class="table-responsive">
                                <table class="table table-striped" id="ordersTable">
                                    <thead>
                                        <tr>
                                            <th>Số thứ tự</th>
                                            <th>Khách hàng</th>
                                            <th>Email</th>
                                            <th>Trạng thái</th>
                                            <th>Tổng cộng</th>
                                            <th>Phương thức thanh toán</th>
                                            <th>Ngày tạo</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @include('admin.order.order_list', ['orders' => $orders])
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="pagination">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Bộ lọc nâng cao</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('admin.orders.list') }}">
                        <!-- Group Status -->
                        <div class="mb-3">
                            <label for="group_status" class="form-label">Nhóm trạng thái</label>
                            <select name="group_status" class="form-control">
                                <option value="">Chọn nhóm trạng thái</option>
                                @foreach ($groupStatuses as $groupStatus)
                                    <option value="{{ $groupStatus }}"
                                        {{ request('group_status') == $groupStatus ? 'selected' : '' }}>
                                        {{ $groupStatus }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Specific Status -->
                        <div class="mb-3">
                            <label for="status_id" class="form-label">Trạng thái cụ thể</label>
                            <select name="status_id" class="form-control">
                                <option value="">Chọn trạng thái cụ thể</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}"
                                        {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Tìm kiếm theo mã</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                placeholder="Tìm kiếm theo mã, tên, email">
                        </div>
                        <div class="row">
                            <div class="mb-3 col-5">
                                <label for="min_total" class="form-label">Tổng cộng tối thiểu</label>
                                <input type="number" name="min_total" class="form-control"
                                    value="{{ request('min_total') }}" min="0" max="1000000000" step="0.01">
                                @error('min_total')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Max Total -->
                            <div class="mb-3 col-5">
                                <label for="max_total" class="form-label">Tổng cộng tối đa</label>
                                <input type="number" name="max_total" class="form-control"
                                    value="{{ request('max_total') }}" min="0" max="1000000000" step="0.01">
                                @error('max_total')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-2">
                                <label for="sort_order" class="form-label">Sắp xếp</label>
                                <select name="sort_order" class="form-control">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>
                                        Giảm dần
                                    </option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>
                                        Tăng dần
                                    </option>
                                </select>
                            </div>
                        </div>
                        <!-- Min Total -->

                        <!-- Date From -->
                        <div class="mb-3">
                            <label for="date_from" class="form-label">Ngày bắt đầu</label>
                            <input type="date" name="date_from" class="form-control"
                                value="{{ request('date_from') }}" min="{{ now()->subYears(2)->toDateString() }}"
                                max="{{ now()->toDateString() }}">
                            @error('date_from')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Date To -->
                        <div class="mb-3">
                            <label for="date_to" class="form-label">Ngày kết thúc</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"
                                min="{{ now()->subYears(2)->toDateString() }}" max="{{ now()->toDateString() }}">
                            @error('date_to')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Clear Date Filters -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-link" id="clearDateFilters">Xem tất cả (Không giới hạn
                                thời gian)</button>
                        </div>
                        <!-- Sort Order -->

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
                            <button type="button" class="btn btn-secondary" id="clearFilters">Xóa bộ lọc</button>
                        </div>
                    </form>
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
    @vite('resources/js/admin/listOrder.js')
@endpush
