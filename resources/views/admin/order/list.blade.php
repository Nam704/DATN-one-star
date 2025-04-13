@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="tab-pane fade show active" id="orders">
                        <div class="card-header">
                            <h3>Orders > List</h3>

                            <!-- Summary Metrics -->
                            <div class="summary row mb-1">
                                <div class="col-md-3">
                                    <div class="widget-flat text-bg-info">
                                        <div class="card-body">
                                            <h5>Total Orders</h5>
                                            <p>{{ $totalOrders }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card widget-flat text-bg-info">
                                        <div class="card-body">
                                            <h5>Open Orders</h5>
                                            <p>{{ $openOrders }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card widget-flat text-bg-info">
                                        <div class="card-body">
                                            <h5>Average Price</h5>
                                            <p>{{ number_format($averagePrice, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card widget-flat text-bg-info">
                                        <div class="card-body">
                                            <h5>Total Revenue</h5>
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
                                                    href="?group_status=All">All</a>
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
                                            placeholder="Search by code, name, email" value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#filterModal">
                                            <i class="mdi mdi-filter-menu fs-5"></i> Filter Menu
                                        </button>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Orders Table -->
                            <div class="table-responsive">
                                <table class="table table-striped" id="ordersTable">
                                    <thead>
                                        <tr>
                                            <th>Number</th>
                                            <th>Customer</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Shipping Cost</th>
                                            <th>Payment Method</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>{{ $order->code }}</td>
                                                <td>{{ json_decode($order->user_data, true)['name'] ?? 'N/A' }}</td>
                                                <td>{{ json_decode($order->user_data, true)['email'] ?? 'N/A' }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $order->orderStatus->group_status == 'Delivered' ? 'bg-success' : ($order->orderStatus->group_status == 'Cancelled' ? 'bg-danger' : 'bg-warning') }}">
                                                        {{ $order->orderStatus->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format($order->total, 2) }}</td>
                                                <td>{{ number_format($order->shipping, 2) }}</td>
                                                <td>{{ $order->payment_method }}</td>
                                                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.orders.detail', $order->id) }}"
                                                        class="btn btn-sm btn-info">View</a>
                                                    @if ($order->orderStatus->name == 'Cancel Requested')
                                                        <a href="{{-- route('admin.orders.approveCancel',$order->id) --}}"
                                                            class="btn btn-sm btn-success">Approve Cancel</a>
                                                    @endif
                                                    <a href="{{-- route('admin.orders.updateStatus',$order->id) --}}" class="btn btn-sm btn-warning">Update
                                                        Status</a>
                                                </td>
                                            </tr>
                                        @endforeach
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
                    <h5 class="modal-title" id="filterModalLabel">Advanced Filters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('admin.orders.list') }}">
                        <!-- Group Status -->
                        <div class="mb-3">
                            <label for="group_status" class="form-label">Group Status</label>
                            <select name="group_status" class="form-control">
                                <option value="">Select Group Status</option>
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
                            <label for="status_id" class="form-label">Specific Status</label>
                            <select name="status_id" class="form-control">
                                <option value="">Select Specific Status</option>
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
                            <label for="search" class="form-label">Search by code, name, email</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                placeholder="Search by code, name, email">
                        </div>
                        <!-- Min Total -->
                        <div class="mb-3">
                            <label for="min_total" class="form-label">Min Total</label>
                            <input type="number" name="min_total" class="form-control"
                                value="{{ request('min_total') }}" placeholder="Min Total">
                        </div>
                        <!-- Max Total -->
                        <div class="mb-3">
                            <label for="max_total" class="form-label">Max Total</label>
                            <input type="number" name="max_total" class="form-control"
                                value="{{ request('max_total') }}" placeholder="Max Total">
                        </div>
                        <!-- Date From -->
                        <div class="mb-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control"
                                value="{{ request('date_from') }}">
                        </div>
                        <!-- Date To -->
                        <div class="mb-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <!-- Clear Date Filters -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-link" id="clearDateFilters">Xem tất cả (Không giới hạn
                                thời gian)</button>
                        </div>
                        <!-- Sort Order -->
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <select name="sort_order" class="form-control">
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending
                                </option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending
                                </option>
                            </select>
                        </div>
                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <button type="button" class="btn btn-secondary" id="clearFilters">Clear</button>
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
    @vite('resources/js/order.js')
@endpush
