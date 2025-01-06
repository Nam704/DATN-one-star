@extends('admin.layouts.layout')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 mt-2   ">Sửa sản phẩm</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{route('admin.products.editPutProduct',$product->id)}}" method="post" enctype="multipart/form-data" class="form">
                @csrf
                @method('PUT') 

                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Nhập tên sản phẩm..." value="{{ old('name', $product->name) }}">
                    @error('name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="id_brand">Brand</label>
                    <select class="form-control @error('id_brand') is-invalid @enderror" id="id_brand" name="id_brand">
                        <option selected>--Chọn thương hiệu---</option>
                        @foreach($brands as $brand)
                        <option value="{{$brand->id}}" {{ $product->id_brand == $brand->id ? 'selected' : '' }}>{{$brand->name}}</option>
                        @endforeach
                    </select>
                    @error('id_brand')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="id_category">Danh mục</label>
                    <select class="form-control @error('id_category') is-invalid @enderror" id="id_category" name="id_category">
                        <option selected>--Chọn danh mục---</option>
                        @foreach($categories as $category)
                        <option value="{{$category->id}}" {{ $product->id_category == $category->id ? 'selected' : '' }}>{{$category->name}}</option>
                        @endforeach
                    </select>
                    @error('id_category')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" rows="5" id="description" name="description" placeholder="Nhập mô tả...">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="hinh_anh" class="form-label ">Tải ảnh lên:</label>
                    <input type="file" name="image_primary" id="image_primary" class="form-control-file border @error('image_primary') is-invalid @enderror">
                    @error('image_primary')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @if ($product->image_primary)
                    <div>
                        <img src="{{ asset('storage/'.$product->image_primary) }}" alt="Current Image" width="100">
                    </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="status">Status</label>
                    <select class="form-select" name="status">
                        <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div>
                    <button type="submit" name="submit" class="btn btn-success">Xác nhận</button>
                    <a href="{{ route('admin.products.listProduct') }}"><button type="button" class="btn btn-secondary">Quay lại</button></a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection
