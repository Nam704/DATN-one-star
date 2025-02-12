@extends('admin.layouts.layout')
@section('content')
{{-- <h2>{{ $title }}</h2> --}}
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">List variant from {{ $product ?$product->name:"" }}</h4>
                    @if (session('success'))
                    <p class="alert alert-primary">
                        {{ session('success') }}
                    </p>
                    @endif

                    <a href="{{-- route('admin.suppliers.getFormAdd') --}}" type="button"
                        class="btn btn-sm btn-primary">
                        Add new variant
                    </a>

                </div>

                <div class="card-body">

                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                            <tr>

                                <th>SKU</th>
                                <th>Status</th>
                                <th>Quantity</th>
                                <th>Action</th>

                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($productVariants as $variant)
                            <tr>
                                <td>{{ $variant->sku }}</td>
                                <td>{{ $variant->status }}</td>
                                <td>{{ $variant->quantity }}</td>
                                <td>
                                    <a type="button" class="btn btn-danger rounded-pill"
                                        href="{{-- route('admin.suppliers.lockOrActive',$supplier->id) --}}">
                                        @if ($variant->status=='active')
                                        {{ 'Lock' }}
                                        @else
                                        {{ 'Active' }}
                                        @endif
                                    </a>
                                    <a type="button" class="btn btn-warning rounded-pill"
                                        href="{{-- route('admin.suppliers.getFormUpdate',$supplier->id) --}}">Edit</a>
                                </td>

                            </tr>
                            @endforeach


                        </tbody>
                        <tfoot>
                            <tr>
                                <th>SKU</th>
                                <th>Status</th>
                                <th>Quantity</th>
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
@endpush