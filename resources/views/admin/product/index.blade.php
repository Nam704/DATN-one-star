@extends('admin.layouts.layout')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="mt-4">

                    <h1 class="text-center">Product Audit</h1>

                    @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                </div>


                <div class="card-body">
                    <div class="mb-3">
                        <a href="{{ route('admin.products.addProduct') }}" class="btn btn-primary">Thêm mới</a>
                    </div>


                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped w-100">
                        <thead>
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
                        @foreach ($products as $key => $product)
                        <tr>
                            <td class="align-middle text-center"><input type="checkbox" name="select[]" id="" value=""></td>
                            <td>{{ $key+1 }}</td>
                            <td>XSM-{{ $key+1 }} </td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->brand->name }}</td>
                            <td>{{$product->category->name }}</td>
                            <td>{{ $product->description }}</td>
                            <td><img src="{{Storage::url($product->image_primary) }}" alt="err" height="60px"></td>
                            <td>{{ $product->status }}</td>
                            <td>{{ $product->created_at }}</td>
                            <td>{{ $product->updated_at }}</td>

                            <td>
                                <a href="{{route('admin.products.editProduct', $product->id)}}"><button type="button" class="btn btn-secondary btn-sm">Sửa</button></a>|

                                <form action="{{route('admin.products.deleteProduct', $product->id)}}" class="d-inline" method="POST" onclick="return confirm('Ban co muon xoa khong')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

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