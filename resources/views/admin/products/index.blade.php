@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle me-1"></i>
                            Add Product
                        </a>
                        <a href="{{ route('admin.products.trash') }}" class="btn btn-warning">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Trash
                        </a>
                    </div>
                    <h4 class="page-title">Products Management</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->brand->name }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($product->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="ri-pencil-line"></i>
                                                </a>
                                                <button type="button" 
                                                    class="btn btn-sm {{ $product->status === 'active' ? 'btn-success' : 'btn-danger' }} toggle-status"
                                                    data-id="{{ $product->id }}">
                                                    <i class="ri-lock{{ $product->status === 'active' ? '-unlock' : '' }}-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-product"
                                                    data-id="{{ $product->id }}">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<x-admin.data-table-styles />
@endpush

@push('scripts')
<x-admin.data-table-scripts />
<script src="{{ asset('admin/api/products.js') }}"></script>
@endpush
