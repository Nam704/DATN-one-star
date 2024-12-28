@extends('admin.layouts.layout')
@section('content')
    <!-- Begin Page Content -->
    @if (session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success')}}   
    </div>
        
    @endif
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800 mb-5">Danh sách danh mục</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <button type="button" class="btn btn-secondary btn-sm" onclick="">Chọn tất cả</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="">Bỏ chọn tất cả</button>
                <button type="submit" name="xoacacmucchon" class="btn btn-secondary btn-sm">Xóa các mục đã chọn</button>
                <a href="{{route('admin.category.addcategory')}}"><button type="button" class="btn btn-secondary btn-sm">Nhập thêm</button></a>
                <div class="float-right">
                    <div class="input-group">
                        <input type="text" class="form-control" name="kyw" placeholder="Tìm kiếm...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" name="search">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th></th>
                                <th>Stt</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Update At</th>
                                <th>Delete AT</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr>
                                    <td class="text-center"><input type="checkbox" name="select[]" value=""></td>
                                    <td class="col-1"></td>
                                    <td class="col-2"></td>
                                    <td class="col-2"></td>
                                    <td class="col-2"></td>
                                    <td class="col-2"></td>
                                    <td class="col-2"></td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>
<!-- /.container-fluid -->
@endsection
