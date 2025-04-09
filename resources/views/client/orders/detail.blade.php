@extends('client.layouts.home.layout')
@section('title', 'Order Detail')
@section('content')

    <div class="container">
        <!-- Title -->
        <div class="d-flex justify-content-between align-items-center py-3">
            <h2 class="h5 mb-0"><a href="#" class="text-muted"></a> Order #{{ $order['code'] }}</h2>
            <input type="hidden" value="{{ $order['id'] }}" id="order_id">
        </div>

        <!-- Main content -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Details -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <div>
                                <span class="me-3">{{ $order['created_at'] }}</span>
                                <span class="me-3">#{{ $order['code'] }}</span>
                                <span class="me-3">{{ $order['payment_method'] }}</span>
                                <span
                                    class="badge rounded-pill bg-info">{{ $order['order_status']['name'] ?? 'Chưa có trạng thái' }}</span>
                            </div>
                        </div>
                        <table class="table table-borderless">
                            <tbody>
                                @foreach ($order['order_details'] as $detail)
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
                                        <td class="text-end">{{ $detail['total'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Subtotal</td>
                                    <td class="text-end">{{ $order['subtotal'] }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Shipping</td>
                                    <td class="text-end">{{ $order['shipping'] }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Discount (Code: {{ $order['voucher_code'] ?? 'N/A' }})</td>
                                    <td class="text-danger text-end">{{ $order['voucher_code'] ?? '' }}</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td colspan="2">TOTAL</td>
                                    <td class="text-end">{{ $order['total'] }}</td>
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
                                <p>{{ $order['payment_method'] }} <br>
                                    Total: {{ $order['total'] }} <span
                                        class="badge bg-success rounded-pill">{{ $order['payment_status'] }}</span></p>
                                @if ($order['payment_status'] != 'Paid')
                                    <a class="btn btn-info" id="retry_payment">Retry Payment</a>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <h3 class="h6">Billing Address</h3>
                                <address>
                                    <strong>{{ $order['user_name'] ?? 'N/A' }}</strong><br>
                                    <p>{{ $order['address'] }}, {{ $order['ward'] }}, {{ $order['district'] }},
                                        {{ $order['province'] }}</p>
                                    <abbr title="Phone">P:</abbr> {{ $order['user_phone'] ?? 'N/A' }}
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
                        <p>{{ $order['note'] ?? 'No notes' }}</p>
                    </div>
                </div>
                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="h6">Shipping Information</h3>
                        <strong>FedEx</strong><br>
                        <span><a href="#" class="text-decoration-underline" target="_blank">FF1234567890</a> <i
                                class="bi bi-box-arrow-up-right"></i></span>
                        <hr>
                        <h3 class="h6">Address</h3>
                        <address>
                            <strong>{{ $order['user_name'] ?? 'N/A' }}</strong><br>
                            <p>{{ $order['address'] }}, {{ $order['ward'] }}, {{ $order['district'] }},
                                {{ $order['province'] }}</p>
                            <abbr title="Phone">P:</abbr> {{ $order['user_phone'] ?? 'N/A' }}
                        </address>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
