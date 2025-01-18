@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="mt-4">

                        <h1 class="text-center">IMAGE</h1>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                    </div>


                    <div class="card-body">
                        <div class="mb-3">
                            <a href="{{ route('admin.images.create') }}" class="btn btn-primary">Thêm mới hình ảnh</a>
                        </div>


                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Hình ảnh</th>
                                    <th>Product Variant</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            @foreach ($images as $image)
                                <tr>
                                    <td>{{ $image->id }}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $image->url) }}" width="100" alt="Image">
                                    </td>
                                    <td>{{ $image->productVariant->sku }}</td>
                                    <td>{{ $image->status }}</td>
                                    <td>
                                        <a href="{{ route('admin.images.edit', $image->id) }}" class="btn btn-warning">Chỉnh
                                            sửa</a>
                                        <a href="{{ route('admin.images.show', $image->id) }}" class="btn btn-primary">Chi
                                            Tiết</a>
                                        <a href="{{ route('admin.images.destroy', $image->id) }}" class="btn btn-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">Xóa</a>
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