@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Chi tiết biến thể: {{ $variant->sku }}</h4>
                <div class="mt-2">
                    <label for="statusFilter">Lọc theo trạng thái đơn hàng:</label>
                    <select id="statusFilter" class="form-control w-25" onchange="filterByStatus()">
                        <option value="">Tất cả</option>
                        @foreach ($allStatuses as $status)
                            <option value="{{ $status }}" {{ $selectedStatus == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body">
                <h5>Danh sách người dùng đã đặt hàng</h5>
                <table id="fixed-header-datatable"  class="table table-striped dt-responsive nowrap table-striped  w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên người dùng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>ID Đơn hàng</th>
                            <th>Trạng thái đơn hàng</th>
                            <th>Địa chỉ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $userData)
                            @foreach ($userData['orders'] as $order)
                                @if (!$selectedStatus || in_array($selectedStatus, $order['statuses'])) 
                                    <tr>
                                        <td>{{ $userData['user']->id }}</td>
                                        <td>{{ $userData['user']->name }}</td>
                                        <td>{{ $userData['user']->email }}</td>
                                        <td>{{ $userData['user']->phone }}</td>
                                        <td>{{ $order['order_id'] }}</td> <!-- Hiển thị ID đơn hàng -->
                                        <td>{{ implode(', ', $order['statuses']) }}</td> <!-- Hiển thị trạng thái -->
                                        <td>{{ $userData['address']->details($userData['address']->id_ward) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function filterByStatus() {
            let selectedStatus = document.getElementById('statusFilter').value;
            let url = new URL(window.location.href);
            if (selectedStatus) {
                url.searchParams.set('status', selectedStatus);
            } else {
                url.searchParams.delete('status');
            }
            window.location.href = url.toString();
        }
    </script>
@endsection
@push('styles')
<x-admin.data-table-styles />
@endpush

@push('scripts')
<x-admin.data-table-scripts />
@endpush
