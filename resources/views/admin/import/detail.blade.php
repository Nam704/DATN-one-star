@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Imports</a></li>
                    <li class="breadcrumb-item active">Import Details</li>
                </ol>
            </div>
            <h4 class="page-title">Import Details #{{ $import->id }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{route('admin.imports.list')}}" class="btn btn-sm btn-primary">Back to list</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p><strong>Supplier:</strong> {{ $import->supplier->name }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Import Name:</strong> {{ $import->name }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Import Date:</strong> {{ $import->import_date }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Total Amount:</strong> {{ number_format($import->total_amount, 2) }}</p>
                    </div>
                    <div class="col-md-12">
                        <p><strong>Note:</strong> {{ $import->note }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Import Details</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Variant</th>
                                    <th>Quantity</th>
                                    <th>Price Per Unit</th>
                                    <th>Expected Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($import_details as $detail)
                                <tr>
                                    <td>{{ $detail->product_variant->sku }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ number_format($detail->price_per_unit, 2) }}</td>
                                    <td>{{ number_format($detail->expected_price, 2) }}</td>
                                    <td>{{ number_format($detail->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection