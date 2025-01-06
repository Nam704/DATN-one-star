@extends('admin.layouts.layout')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm mới biến thể sản phẩm</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.productvariant.addPostProductVariant')}}" method="post" class="form">
                @csrf

                <div class="mb-3">
                    <label for="id_parent">Product name</label>
                    <select name="id_product" id="id_product" class="form-control">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                <label for="sku">SKU</label>
                <input type="text" name="sku" class="form-control" id="sku" required>
                </div>
                <div class="mb-3">
                    <label for="status">Status</label>
                    <select class="form-select" aria-label="Default select example" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
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
