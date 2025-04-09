@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <!-- Title -->
        <div class="d-flex justify-content-between align-items-center py-3">
            <h2 class="h5 mb-0">Order #{{ $order->code }}</h2>
        </div>

        <!-- Main content -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Details -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <div>
                                <span class="me-3">{{ $order->created_at }}</span>
                                <span class="me-3">#{{ $order->code }}</span>
                                <span class="me-3">{{ $order->payment_method }}</span>
                                <span class="badge rounded-pill bg-info">{{ $order->orderStatus->name }}</span>
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-link p-0 me-3 btn-icon-text" id="download-invoice">
                                    <i class="bi bi-download"></i> <span class="text">Invoice</span>
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-link p-0 text-muted" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @foreach ($nextStatuses as $status)
                                            <li>
                                                <a class="dropdown-item change-status" href="#"
                                                    data-order-id="{{ $order->id }}"
                                                    data-status-id="{{ $status->id }}">
                                                    <i class="bi bi-arrow-right"></i> {{ $status->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                        <li><a class="dropdown-item" href="#" id="print-order"><i
                                                    class="bi bi-printer"></i> Print</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <table class="table table-borderless">
                            <tbody>
                                @foreach ($order->orderDetails as $detail)
                                    @php
                                        $variant = $detail->productVariant;
                                        $image = $variant->images ? $variant->images->url : 'default-image.jpg';
                                        $values = $variant->attributeValues;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex mb-2">
                                                <div class="flex-shrink-0">
                                                    <img src="{{ asset($image) }}" alt="" width="35"
                                                        class="img-fluid">
                                                </div>
                                                <div class="flex-lg-grow-1 ms-3">
                                                    <h6 class="small mb-0">
                                                        <a href="#"
                                                            class="text-reset">{{ $variant->product->name }}</a>
                                                    </h6>
                                                    @foreach ($values as $value)
                                                        <div>
                                                            <span class="small">{{ $value->attribute_name }}:
                                                                {{ $value->value }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td>Quantity: {{ $detail->quantity }}</td>
                                        <td class="text-end">{{ number_format($detail->total, 2) }} ₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Subtotal</td>
                                    <td class="text-end">{{ number_format($order->subtotal, 2) }} ₫</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Shipping</td>
                                    <td class="text-end">{{ number_format($order->shipping ?? 0, 2) }} ₫</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Discount</td>
                                    <td class="text-danger text-end">
                                        @if ($order->voucher)
                                            {{ number_format($order->subtotal - $order->total + ($order->shipping ?? 0), 2) }}
                                            ₫
                                            (Code: {{ $order->voucher->code }})
                                        @else
                                            0 ₫
                                        @endif
                                    </td>
                                </tr>
                                <tr class="fw-bold">
                                    <td colspan="2">TOTAL</td>
                                    <td class="text-end">{{ number_format($order->total, 2) }} ₫</td>
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
                                <p>{{ $order->payment_method }} <br>
                                    Total: {{ number_format($order->total, 2) }} ₫
                                    <span class="badge bg-success rounded-pill">{{ $order->payment_status }}</span>
                                </p>
                            </div>
                            <div class="col-lg-6">
                                <h3 class="h6">Billing Address</h3>
                                <address>
                                    <strong>{{ $order->user_data['name'] ?? 'N/A' }}</strong><br>
                                    {{ $order->address_data['details'] ?? 'N/A' }}<br>
                                    <abbr title="Phone">P:</abbr> {{ $order->user_data['phone'] ?? 'N/A' }}
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
                        <p>{{ $order->note ?? 'No notes' }}</p>
                    </div>
                </div>
                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="h6">Shipping Information</h3>
                        <strong>FedEx</strong><br>
                        <span><a href="#" class="text-decoration-underline">FF1234567890</a></span>
                        <hr>
                        <h3 class="h6">Address</h3>
                        <address>
                            <strong>{{ $order->user_data['name'] ?? 'N/A' }}</strong><br>
                            {{ $order->address_data['details'] ?? 'N/A' }}<br>
                            <abbr title="Phone">P:</abbr> {{ $order->user_data['phone'] ?? 'N/A' }}
                        </address>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script xử lý hành động -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $(document).ready(function() {
            // In đơn hàng
            $('#print-order').on('click', function(e) {
                e.preventDefault();
                window.print();
            });

            // Tải invoice (giả lập)
            $('#download-invoice').on('click', function(e) {
                e.preventDefault();
                alert('Downloading invoice...'); // Thay bằng logic thực tế
            });

            // Thay đổi trạng thái đơn hàng
            $('.change-status').on('click', function(e) {
                e.preventDefault();
                const orderId = $(this).data('order-id');
                const statusId = $(this).data('status-id');
                const statusName = $(this).text().trim();

                if (confirm(`Bạn có muốn chuyển trạng thái đơn hàng sang "${statusName}" không?`)) {
                    axios.post('/api/orders/' + orderId + '/update-status', {
                            status_id: statusId
                        })
                        .then(response => {
                            if (response.data.success) {
                                alert('Trạng thái đơn hàng đã được cập nhật!');
                                location.reload(); // Tải lại trang để cập nhật giao diện
                            } else {
                                alert('Lỗi: ' + response.data.message);
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            alert('Đã xảy ra lỗi khi cập nhật trạng thái.');
                        });
                }
            });
        });
    </script>
@endsection
