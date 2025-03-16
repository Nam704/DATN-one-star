@extends('client.layouts.home.layout')
@section('title', 'Order Detail')
@section('content')

    <div class="container">
        <!-- Title -->
        <div class="d-flex justify-content-between align-items-center py-3">
            <h2 class="h5 mb-0"><a href="#" class="text-muted"></a> Order #{{ $order->code }}</h2>
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
                                <button class="btn btn-link p-0 me-3 d-none d-lg-block btn-icon-text"><i
                                        class="bi bi-download"></i> <span class="text">Invoice</span></button>
                                <div class="dropdown">
                                    <button class="btn btn-link p-0 text-muted" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-pencil"></i> Edit</a>
                                        </li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <table class="table table-borderless">
                            <tbody>
                                @foreach ($order->orderDetails as $detail)
                                    @php
                                        // dd($detail);
                                        $variant = $detail->productVariant;
                                        $image = $variant->images->url;
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
                                                        <a href="#" class="text-reset">
                                                            {{ $variant->product->name }}
                                                        </a>
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
                                        <td class="text-end">{{ $detail->total }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <tr>

                                    <td colspan="2">Subtotal</td>
                                    <td class="text-end">{{ $order->subtotal }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Shipping</td>
                                    <td class="text-end">{{ $order->shipping }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Discount (Code: {{ $order->id_voucher }})</td>
                                    <td class="text-danger text-end">{{ $order->id_voucher ?? '' }}</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td colspan="2">TOTAL</td>
                                    <td class="text-end">{{ $order->total }}</td>
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
                                    Total: {{ $order->total }} <span
                                        class="badge bg-success rounded-pill">{{ $order->payment_status }}</span></p>
                            </div>
                            <div class="col-lg-6">
                                <h3 class="h6">Billing address</h3>
                                <address>
                                    <strong>{{ $order->user_name }}</strong>
                                    <br>
                                    <p> {{ $order->address->details($order->id_ward) }}</p>

                                    <abbr title="Phone">P:</abbr> {{ $order->phone_number }}
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
                <div class="card mb-4">
                    <!-- Shipping information -->
                    <div class="card-body">
                        <h3 class="h6">Shipping Information</h3>
                        <strong>FedEx</strong>
                        <span><a href="#" class="text-decoration-underline" target="_blank">FF1234567890</a> <i
                                class="bi bi-box-arrow-up-right"></i> </span>
                        <hr>
                        <h3 class="h6">Address</h3>
                        <address>
                            <strong>{{ $order->user_name }}</strong>
                            <p> {{ $order->address->details($order->id_ward) }}</p>
                            <br>
                            <abbr title="Phone">P:</abbr> {{ $order->phone_number }}
                        </address>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
