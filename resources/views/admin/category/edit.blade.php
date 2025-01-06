@extends('admin.layouts.layout')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sửa danh mục</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.categories.editPutCategory', $categories->id) }}" method="post" class="form">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Danh mục</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên danh mục..." value="{{ $categories->name }}">
                    @error('name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="">
                    <label for="id_parent">Danh mục cha:</label>
                    <select name="id_parent" id="id_parent" class="form-control">
                        <option value="">Không có</option>
                        @foreach($category_parent as $parent)
                        <option value="{{ $parent->id }}" {{ $categories->id_parent == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_parent')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="status">Status</label>
                    <select class="form-select" aria-label="Default select example" name="status">
                        <option value="active" {{ $categories->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $categories->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
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