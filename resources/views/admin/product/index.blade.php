@extends('admin.layouts.layout')
@section('content')
    <!-- Begin Page Content -->
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800 mb-5">Danh sách sản phẩm</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <button type="button" class="btn btn-secondary btn-sm" onclick="">Chọn tất cả</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="">Bỏ chọn tất cả</button>
                <button type="submit" name="xoacacmucchon" class="btn btn-secondary btn-sm">Xóa các mục đã chọn</button>
                <a href="{{ route('admin.products.addProduct') }}"><button type="button" class="btn btn-secondary btn-sm">Nhập thêm</button></a>
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
                                <th>STT</th>
                                <th>Product code</th>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Created_at</th>
                                <th>Updated_at</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $key => $product)
                                <tr>
                                    <td class="align-middle text-center"><input type="checkbox" name="select[]" id="" value=""></td>
                                    <td class="col-1 align-middle">{{ $key+1 }}</td>
                                    <td class="col-1 align-middle">XSM-{{ $key+1 }} </td>
                                    <td class="col-2 align-middle">{{ $product->name }}</td>
                                    <td class="col-1 align-middle">{{ $product->brand->name }}</td>
                                    <td class="col-1 align-middle">{{$product->category->name  }}</td>
                                    <td  class="align-middle">{{ $product->description }}</td>
                                    <td  class="col-1 align-middle"><img src="{{Storage::url($product->image_primary) }}" alt="err" height="60px"></td>
                                    <td  class="col-1 align-middle">{{ $product->status }}</td>
                                    <td  class="col-1 align-middle">{{ $product->created_at }}</td>
                                    <td  class="col-1 align-middle">{{ $product->updated_at }}</td>

                                    <td class="col-2 align-middle">
                                        <a href="{{route('admin.products.editProduct', $product->id)}}"><button type="button" class="btn btn-secondary btn-sm">Sửa</button></a>|

                                        <form action="{{route('admin.products.deleteProduct', $product->id)}}" class="d-inline" method="POST" onclick="return confirm('Ban co muon xoa khong')">
                                         @csrf
                                         @method('DELETE')
                                         <button type="submit" class="btn btn-secondary btn-sm">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>   
                </div>
            </div>
        </div>
</div>
<!-- /.container-fluid -->
@endsection