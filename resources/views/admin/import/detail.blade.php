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
            <div>
                @if (session('success'))
                <p class="alert alert-primary">
                    {{ session('success') }}
                </p>
                @endif
                @if ($errors->any()||session('error'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                        @if (session('error'))
                        <li>{{ session('error') }}</li>
                        @endif
                    </ul>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{route('admin.imports.listApproved')}}" class="btn btn-sm btn-primary">Back to list</a>
<<<<<<< HEAD
                @if ($import->status == 'approved')
                <a href="{{ route('admin.imports.updatePrice',$import->id) }}" class="btn btn-info">Cập nhật giá</a>
                @endif
=======
>>>>>>> 41311ec196c676571d9d1b179eb17a190b2f9c31
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
                @if ($import->status == 'pending')
                <div class="row col-12 mt-4 d-flex justify-content-between">
                    <a href="{{ route('admin.imports.accept',['id'=>$import->id]) }}"
                        class="btn btn-primary btn-sm col-5">
                        <i class="fas fa-trash"></i>
                        Accept
                    </a>
                    <a href="{{ route('admin.imports.reject',['id'=>$import->id]) }}"
                        class="btn btn-danger btn-sm col-5">
                        <i class="fas fa-trash"></i>
                        Reject
                    </a>
                </div>
                @endif

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