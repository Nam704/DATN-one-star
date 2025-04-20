<div class="tab-pane fade show active" id="orders">
    <h3>Orders > List</h3>

    <!-- Summary Metrics -->
    <div class="summary row mb-4">
        <div class="col-md-4">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5>Total Orders</h5>
                    <p>{{ $totalOrders }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5>Open Orders</h5>
                    <p>{{ $openOrders }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5>Average Price</h5>
                    <p>{{ number_format($averagePrice, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="filters mb-4">
        <form method="GET" action="{{ route('client.user.myAccount') }}" class="row g-3">
            <!-- Status Tabs -->
            <div class="col-12">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'All' || !request('status') ? 'active' : '' }}"
                            href="?status=All">All</a>
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

            <!-- Advanced Filters -->
            <div class="col-md-3">
                <select name="group_status" class="form-control">
                    <option value="">Group Status</option>
                    @foreach ($groupStatuses as $groupStatus)
                        <option value="{{ $groupStatus }}"
                            {{ request('group_status') == $groupStatus ? 'selected' : '' }}>
                            {{ $groupStatus }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by code or name"
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="min_total" class="form-control" placeholder="Min Total"
                    value="{{ request('min_total') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="max_total" class="form-control" placeholder="Max Total"
                    value="{{ request('max_total') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="table-responsive">
        <table class="table table-light table-striped">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->code }}</td>
                        <td>{{ json_decode($order->user_data, true)['name'] ?? 'N/A' }}</td>
                        <td>
                            <span
                                class="badge {{ $order->orderStatus->group_status == 'Delivered' ? 'bg-success' : ($order->orderStatus->group_status == 'Cancelled' ? 'bg-danger' : 'bg-warning') }}">
                                {{ $order->orderStatus->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>{{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td>
                            <a href="{{ route('client.orders.detail', $order->id) }}"
                                class="btn btn-sm btn-info">Xem</a>
                            @php
                                $retryPaymentService = app(\App\Services\RetryPaymentService::class);
                                $canRetry = $retryPaymentService->canRetryPayment($order->id);
                            @endphp
                            @if ($canRetry['success'])
                                <a href="{{ route('client.orders.retryPayment', $order->id) }}"
                                    class="btn btn-sm btn-warning">Thanh toán lại</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>{{ $orders->links() }}</div>
</div>
