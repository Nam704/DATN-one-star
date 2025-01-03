@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center">
                    <h1 class="mt-2">Chi Tiết Hình Ảnh</h1>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="form-group">
                        <label for="url">Hình Ảnh</label>
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $image->url) }}" class="img-fluid rounded" alt="Hình ảnh">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="id_product_variant">Sản Phẩm</label>
                        <p>{{ $image->productVariant->sku ?? 'Không xác định' }}</p>
                    </div>

                    <div class="form-group">
                        <label for="status">Trạng Thái</label>
                        <p>
                            <span class="badge {{ $image->status === 'active' ? 'badge-success' : 'badge-danger' }} text-dark">
                                {{ ucfirst($image->status) }}
                            </span>
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="created_at">Ngày Tạo</label>
                        <p>{{ $image->created_at->format('d-m-Y H:i:s') }}</p>
                    </div>

                    <div class="form-group">
                        <label for="updated_at">Cập Nhật Lần Cuối</label>
                        <p>{{ $image->updated_at->format('d-m-Y H:i:s') }}</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.images.index') }}" class="btn btn-secondary">Quay Lại</a>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div> <!-- end row -->
</div> <!-- end container-fluid -->
@endsection

@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
@endpush
