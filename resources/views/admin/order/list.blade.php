@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">List Order</h4>
                    </div>

                    <div class="card-body">

                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped  w-100">
                            <div class="row">
                                <select name="status" id="statuses" class="col-3">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                                <div class="col-2">
                                    <button class="accept-all">Accept</button>
                                    <button class="reject-all">Reject</button>
                                </div>
                            </div>

                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>Code</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody class="order_list">
                                @foreach ($orders as $order)
                                    <tr>
                                        <td><input type="checkbox" name="selected_order[]" value="{{ $order->id }}"
                                                class="import-checkbox"></td>
                                        <td>
                                            {{ $order->code }}
                                        </td>
                                        <td>
                                            {{ $order->created_at }}
                                        </td>
                                        <td>
                                            {{ $order->total }}
                                        </td>
                                        <td>
                                            {{ $order->payment_method }} : {{ $order->payment_status }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.detail', $order->id) }}"
                                                class="btn  btn-primary">Detail</a>
                                            <a href="#" data-order-id="{{ $order->id }}"
                                                class="btn btn-warning accept">Accept</a>
                                            <a href="{{-- route('admin.orders.destroy',$order->id) --}}" class="btn  btn-danger">Reject</a>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>Code</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->
    </div>
@endsection
@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
    @vite('resources/js/order.js')
@endpush
