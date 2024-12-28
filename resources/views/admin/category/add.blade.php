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
        <form action="" method="post" class="form">
            @csrf
            
            <div class="mb-3">
                <label for="" class="form-label">Mã loại</label>
                <input type="text" name="id" id="" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Danh mục</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên danh mục..." value="{{old('name')}}">
                @error('name')
                        <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <button type="submit" name="submit" class="btn btn-success">Xác nhận</button>
                <a href="{{route('admin.category.listcategory')}}"><button type="button" class="btn btn-success">Quay lại</button></a>
            </div>
        </form>
    </div>
</div>


</div>
<!-- /.container-fluid -->
@endsection
