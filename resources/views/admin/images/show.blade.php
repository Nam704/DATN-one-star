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
                    <form action="{{ route('admin.images.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="url">Chọn ảnh</label>
                            <div class="col-md-4">
                                <img src="{{ asset('storage/' . $image->url) }}" class="img-fluid rounded"
                                    alt="Hình ảnh">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="id_product_variant">Sản phẩm</label>
                            <select name="id_product_variant" class="form-control" required disabled>
                                @foreach ($productVariants as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->sku }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Trạng thái</label>
                            <select name="status" class="form-control" required disabled>
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
                            </select>
                        </div>

                        <div class="mt-2">
                            <a type="submit" href="{{ route('admin.images.index') }}" class="btn btn-primary">Về trang
                                chủ</a>
                        </div>
                    </form>
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