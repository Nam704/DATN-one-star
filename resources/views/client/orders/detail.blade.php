@extends('client.layouts.home.layout')
@section('title', 'Order Detail')
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
                            <div id="order-title">
                                <span class="me-3">{{ $orderDetails['created_at'] }}</span>
                                <span class="me-3">#{{ $orderDetails['code'] }}</span>
                                <span class="me-3">{{ $orderDetails['payment_method'] }}</span>
                                <span
                                    class="badge rounded-pill bg-info order-status">{{ $orderDetails['order_status']['name'] ?? 'Chưa có trạng thái' }}</span>
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
                                        <td>Quantity: {{ $detail['quantity'] }}</td>
                                        <td class="text-end"> {{ number_format($detail['total'], 0, ',', '.') }} ₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Subtotal</td>
                                    <td class="text-end">{{ number_format($orderDetails['subtotal'], 0, ',', '.') }} ₫</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Shipping</td>
                                    <td class="text-end">{{ number_format($orderDetails['shipping'], 0, ',', '.') }} ₫</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Discount (Code: {{ $orderDetails['voucher_code'] ?? 'N/A' }})</td>
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
                                    <td colspan="2">TOTAL</td>
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
                                <h3 class="h6">Payment Method</h3>
                                <p>{{ $orderDetails['payment_method'] }} <br>
                                    Total: {{ number_format($orderDetails['total'], 0, ',', '.') ?? '' }} ₫ <span
                                        class="badge bg-success rounded-pill">{{ $orderDetails['payment_status'] }}</span>
                                </p>
                            </div>
                            <div class="col-lg-6">
                                <h3 class="h6">Billing Address</h3>
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
                        <h3 class="h6">Customer Notes</h3>
                        <p>{{ $orderDetails['note'] ?? 'No notes' }}</p>
                    </div>
                </div>
                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-body">
                        @if (in_array($orderDetails['order_status']['name'], ['Cancel Requested', 'Cancel Under Review']))
                            <div class="alert alert-info">
                                Yêu cầu hủy đơn hàng đang được xử lý. Trạng thái:
                                {{ $orderDetails['order_status']['name'] }}
                            </div>
                        @elseif ($orderDetails['order_status']['name'] === 'Cancel Approved')
                            <div class="alert alert-success">
                                Yêu cầu hủy đơn hàng đã được phê duyệt.
                            </div>
                        @elseif ($orderDetails['order_status']['name'] === 'Cancel Rejected')
                            <div class="alert alert-danger">
                                Yêu cầu hủy đơn hàng đã bị từ chối.
                            </div>
                        @endif
                    </div>
                </div>
                <!-- Order actions -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="h6">Hành động đơn hàng</h3>
                        <div id="order-actions">
                            <!-- Nút thanh toán lại -->
                            @php
                                $retryPaymentService = app(\App\Services\RetryPaymentService::class);
                                $canRetry = $retryPaymentService->canRetryPayment($order->id);
                            @endphp
                            @if ($canRetry['success'])
                                <button type="submit" class="btn btn-warning mb-2 retry-payment-btn" id="retry-payment"
                                    data-id="{{ $order->id }}" data-can-retry="true">
                                    Thanh toán lại
                                </button>
                            @elseif (
                                !$canRetry['success'] &&
                                    in_array($orderDetails['order_status']['name'], [
                                        'Payment Failed',
                                        'Payment Expired',
                                        'Payment Retry Requested',
                                    ]))
                                <div class="alert alert-warning mt-2 retry-payment-message">
                                    Không thể thanh toán lại: {{ $canRetry['message'] }}
                                </div>
                            @endif

                            <!-- Nút Đã nhận -->
                            @if (in_array($order->orderStatus->name, ['Shipping']))
                                <button class="btn btn-sm btn-warning update-status received-btn"
                                    data-id="{{ $order->id }}" data-status="shipping">
                                    Đã nhận
                                </button>
                            @endif

                            <!-- Form hủy đơn hàng -->
                            @if ($orderService->canCancelOrder($order->id))
                                <form action="{{ route('client.orders.cancelOrder', $order->id) }}" method="POST"
                                    class="cancel-order-form" data-can-cancel="true">
                                    @csrf
                                    <div class="form-group">
                                        <label for="reason_id">Lý do hủy:</label>
                                        <select name="reason_id" id="reason_id" class="form-control mb-2" required>
                                            <option value="">Chọn lý do</option>
                                            @foreach ($orderService->listReason() as $reason)
                                                <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-danger">Hủy đơn hàng</button>
                                </form>
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
    </div>

@endsection
@section('scripts')
    @vite('resources/js/client/orderDetail.js')
@endsection
