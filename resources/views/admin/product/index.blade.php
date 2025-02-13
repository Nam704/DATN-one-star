@extends('admin.layouts.layout')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Fixed Header</h4>


                    <a href="{{ route('admin.products.addProduct') }}" type="button" class="btn btn-sm btn-primary">
                        Add new
                        product</a>

                </div>

                <div class="card-body">

                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                        <tr>
                                <th>STT</th>
                                <th>Name</th>
                                <th>Brand</th>
                                {{-- <th>Category</th> --}}
                                <th>Description</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach ($products as $key => $product)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td></td>
                            {{-- <td>{{$product->category->name }}</td> --}}
                            <td>{{strip_tags($product->description) }}</td>
                            <td><img src="{{Storage::url($product->image_primary) }}" alt="err" height="60px"></td>
                            <td>{{ $product->status }}</td>

                            <td>
                                <a href="{{route('admin.products.editProduct', $product->id)}}"><button type="button" class="btn btn-secondary btn-warning">Sửa</button></a>|

                                <form action="{{route('admin.products.deleteProduct', $product->id)}}" class="d-inline" method="POST" onclick="return confirm('Ban co muon xoa khong')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach


                        </tbody>
                        <tfoot>
                        <tr>
                                <th>STT</th>
                                <th>Name</th>
                                <th>Brand</th>
                                {{-- <th>Category</th> --}}
                                <th>Description</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
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