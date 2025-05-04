@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <!-- Title -->
        <div class="d-flex justify-content-between align-items-center py-3">
            <h2 class="h5 mb-0"><a href="#" class="text-muted"></a> Order #{{ $orderDetails['code'] }}</h2>
            <input type="hidden" value="{{ $order->id }}" id="order_id">
        </div>

        <!-- Main content -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Details -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <div>
                                <span class="me-3">{{ $orderDetails['created_at'] }}</span>
                                <span class="me-3">#{{ $orderDetails['code'] }}</span>
                                <span class="me-3">{{ $orderDetails['payment_method'] }}</span>
                                <span
                                    class="badge rounded-pill bg-info">{{ $orderDetails['order_status']['name'] ?? 'Chưa có trạng thái' }}</span>
                            </div>
                        </div>
                        <table class="table table-borderless">
                            <tbody>
                                @foreach ($orderDetails['order_details'] as $detail)
                                    <tr>
                                        <td>
                                            <div class="d-flex mb-2">
                                                <div class="flex-shrink-0">
                                                    <img src="{{ asset($detail['image']) }}" alt="" width="35"
                                                        class="img-fluid">
                                                </div>
                                                <div class="flex-lg-grow-1 ms-3">
                                                    <h6 class="small mb-0">
                                                        <a href="#" class="text-reset">
                                                            {{ $detail['name'] }}
                                                        </a>
                                                        <p class="mt-2">{{ $detail['sku'] }}</p>
                                                    </h6>
                                                    @foreach ($detail['attributes'] as $attribute)
                                                        <div>
                                                            <span class="small">{{ $attribute['attribute_name'] }}:
                                                                {{ $attribute['value'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td>Số lượng: {{ $detail['quantity'] }}</td>
                                        <td class="text-end"> {{ number_format($detail['total'], 0, ',', '.') }} ₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Tổng phụ</td>
                                    <td class="text-end">{{ number_format($orderDetails['subtotal'], 0, ',', '.') }} ₫</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Phí vận chuyển</td>
                                    <td class="text-end">{{ number_format($orderDetails['shipping'], 0, ',', '.') }} ₫</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Giảm giá (Mã: {{ $orderDetails['voucher_code'] ?? 'N/A' }})</td>
                                    <td class="text-danger text-end">
                                        @if ($orderDetails['discount'] !== 'N/A')
                                            {{ number_format($orderDetails['discount'], 0, ',', '.') ?? '' }}
                                        @else
                                            0
                                        @endif
                                        ₫
                                    </td>
                                </tr>
                                <tr class="fw-bold">
                                    <td colspan="2">Tổng thanh toán</td>
                                    <td class="text-end">{{ number_format($orderDetails['total'], 0, ',', '.') ?? '' }} ₫
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- Payment -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h3 class="h6">Phương thức thanh toán</h3>
                                <p>{{ $orderDetails['payment_method'] }} <br>
                                    Total: {{ number_format($orderDetails['total'], 0, ',', '.') ?? '' }} ₫ <span
                                        class="badge bg-success rounded-pill">{{ $orderDetails['payment_status'] }}</span>
                                </p>
                            </div>
                            <div class="col-lg-6">
                                <h3 class="h6">Địa chỉ </h3>
                                <address>
                                    <strong>{{ $orderDetails['user_name'] ?? 'N/A' }}</strong><br>
                                    <p>{{ $orderDetails['address'] }}, {{ $orderDetails['ward'] }},
                                        {{ $orderDetails['district'] }}, {{ $orderDetails['province'] }}</p>
                                    <abbr title="Phone">P:</abbr> {{ $orderDetails['user_phone'] ?? 'N/A' }}
                                </address>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Customer Notes -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="h6">Ghi chú khách hàng</h3>
                        <p>{{ $orderDetails['note'] ?? 'No notes' }}</p>
                    </div>
                </div>
                <!-- Cancellation Info -->
                @if ($order->orderCancellations->isNotEmpty())
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h6">Yêu cầu hủy đơn hàng</h3>
                            @foreach ($order->orderCancellations as $cancellation)
                                <p>
                                    <strong>Lý do hủy:</strong> {{ $cancellation->reason->reason ?? 'N/A' }} <br>
                                    <strong>Trạng thái:</strong> {{ ucfirst($cancellation->status) }} <br>
                                    @if ($cancellation->note)
                                        <strong>Ghi chú:</strong> {{ $cancellation->note }} <br>
                                    @endif
                                    <strong>Thời gian yêu cầu:</strong>
                                    {{ $cancellation->created_at->format('d/m/Y H:i') }} <br>
                                    <strong>Thời gian cập nhật:</strong>
                                    {{ $cancellation->updated_at->format('d/m/Y H:i') }}
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Order actions -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="h6">Trạng thái đơn hàng</h3>

                        <!-- Form xử lý yêu cầu hủy -->
                        @if (in_array($order->orderStatus->name, ['Cancel Requested', 'Cancel Under Review']))
                            <form action="{{ route('admin.orders.process_cancellation', $order->id) }}" method="POST">
                                @csrf
                                <div class="form-group mb-2">
                                    <label for="action">Hành động:</label>
                                    <select name="action" id="action" class="form-control" required>
                                        {{-- <option value="">Chọn hành động</option> --}}
                                        <option value="approve" selected>Phê duyệt hủy</option>
                                        {{-- <option value="reject">Từ chối hủy</option> --}}
                                    </select>
                                </div>
                                {{-- <div class="form-group mb-2">
                                    <label for="admin_note">Ghi chú (bắt buộc khi từ chối):</label>
                                    <textarea name="admin_note" id="admin_note" class="form-control"></textarea>
                                </div> --}}
                                <button type="submit" class="btn btn-primary mb-2">Xử lý</button>
                            </form>
                        @endif

                        <!-- Nút cập nhật trạng thái -->
                        @if (
                            !in_array($order->orderStatus->name, [
                                'Shipping',
                                'Delivered',
                                'Cancelled',
                                'Refunded',
                                'Return Rejected',
                                'Payment Retry Requested',
                                'Payment Expired',
                                'Payment Failed',
                                'Paid',
                            ]) &&
                                !in_array($order->orderStatus->group_status, ['Cancelled']) &&
                                $order->orderStatus->nextStatus &&
                                !in_array($order->orderStatus->name, ['Cancel Requested', 'Cancel Under Review']))
                            <button class="btn btn-sm btn-warning update-status" data-id="{{ $order->id }}">Cập nhật trạng thái</button>
                        @endif

                        <!-- Hiển thị thông báo -->
                        @if (session('success'))
                            <div class="alert alert-success mt-2">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @vite('resources/js/admin/detailOrder.js')
@endpush
