@extends('admin.layouts.layout')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 mt-2">Sửa biến thể sản phẩm</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.productvariant.editPutProductVariant',$product_variant->id)}}" method="post" class="form">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="id_parent">Product name</label>
                    <select name="id_product" id="id_product" class="form-control">
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $product->id == $product_variant->id_product ? 'selected' : '' }}>{{$product->name}}</option>
                            @endforeach
                    </select>
                    @error('id_product')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="sku">SKU</label>
                    <input type="text" name="sku" class="form-control" id="sku" required value="{{$product_variant->sku}}">
                    @error('id_product')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="status">Status</label>
                    <select class="form-select" aria-label="Default select example" name="status">
                        <option value="active" {{ $product_variant->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $product_variant->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-success">Xác nhận</button>
                    <a href="{{route('admin.productvariant.listProductVariant')}}"><button type="button" class="btn btn-success">Quay lại</button></a>
                </div>
            </form>
        </div>
    </div>


</div>
<!-- /.container-fluid -->
@endsection
@push('styles')
<x-admin.data-table-styles />
@endpush

@push('scripts')
<x-admin.data-table-scripts />
@endpush
