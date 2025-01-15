@extends('admin.layouts.layout')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Product List</h4>
                    <a href="{{ route('admin.products.addProduct') }}" class="btn btn-sm btn-primary">Add New Product</a>
                </div>

                <div class="card-body">
                    <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($products as $key => $product)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                <td>
                                    @foreach ($product->categories as $category)
                                        {{ $category->name }}@if (!$loop->last), @endif
                                    @endforeach
                                </td>                                <td>{{ $product->description }}</td>
                                <td>
                                    @if ($product->image_primary)
                                        <img src="{{ asset('storage/' . $product->image_primary) }}" alt="Product Image" height="60px">
                                    @else
                                        <span>No Image</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $product->status }}
                                </td>
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
