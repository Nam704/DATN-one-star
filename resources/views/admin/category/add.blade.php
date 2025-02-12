@extends('admin.layouts.layout')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm mới danh mục</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.categories.addPostCategory')}}" method="post" class="form">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Danh mục</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên danh mục..." value="{{old('name')}}">
                    @error('name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="id_parent">Danh mục cha:</label>
                    <select name="id_parent" id="id_parent" class="form-control">
                        <option value="">Không có</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
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
                    <a href="{{route('admin.categories.listCategory')}}"><button type="button" class="btn btn-success">Quay lại</button></a>
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