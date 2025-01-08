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
                        <a href="{{ route('admin.productvariant.addProductVariant') }}" class="btn btn-primary">Thêm mới</a>
                    </div>


                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped w-100">
                        <thead>
                            <tr>
                                <th>Stt</th>
                                <th>Product Name</th>
                                <td>Sku</td>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($product_variant as $key => $variant)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{$variant->product->name}}</td>
                            <td>{{$variant->sku}}</td>
                            <td>{{$variant->status}}</td>
                            <td>
                                <a href="{{route('admin.productvariant.editProductVariant',$variant->id)}}">
                                    <button type="button" class="btn btn-secondary btn-warning">Sửa</button>
                                </a> |
                                <form action="{{route('admin.productvariant.deleteProductVariant',$variant->id)}}" class="d-inline" method="POST" onclick="return confirm('Ban co muon xoa khong')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-danger ">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Stt</th>
                                <th>Product Name</th>
                                <td>Sku</td>
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