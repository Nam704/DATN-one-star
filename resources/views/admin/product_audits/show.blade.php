@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h1 class="mt-2">Chi Tiết Kiểm Tra Sản Phẩm</h1>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <a href="{{ route('admin.product_audits.create') }}" class="btn btn-primary">Thêm mới</a>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="form-group row">
                            <label for="id_user" class="col-sm-3 col-form-label">Người Dùng</label>
                            <div class="col-sm-9">
                                <p>{{ $audit->user->name ?? 'Không xác định' }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="id_product_variant" class="col-sm-3 col-form-label">Sản Phẩm</label>
                            <div class="col-sm-9">
                                <p>{{ $audit->productVariant->name ?? 'Không xác định' }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="quantity" class="col-sm-3 col-form-label">Số Lượng</label>
                            <div class="col-sm-9">
                                <p>{{ $audit->quantity }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="action_type" class="col-sm-3 col-form-label">Loại Hành Động</label>
                            <div class="col-sm-9">
                                <p>
                                    <span class="badge {{ $audit->action_type === 'add' ? 'badge-success' : ($audit->action_type === 'remove' ? 'badge-danger' : 'badge-warning') }} text-dark">
                                        {{ ucfirst($audit->action_type) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="reason" class="col-sm-3 col-form-label">Lý Do</label>
                            <div class="col-sm-9">
                                <p>{{ $audit->reason ?? 'Không có lý do' }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="created_at" class="col-sm-3 col-form-label">Ngày Tạo</label>
                            <div class="col-sm-9">
                                <p>{{ $audit->created_at ? $audit->created_at->format('d-m-Y H:i:s') : 'Không có dữ liệu' }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="updated_at" class="col-sm-3 col-form-label">Cập Nhật Lần Cuối</label>
                            <div class="col-sm-9">
                                <p>{{ $audit->updated_at ? $audit->updated_at->format('d-m-Y H:i:s') : 'Không có dữ liệu' }}</p>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.product_audits.index') }}" class="btn btn-secondary">Quay Lại</a>
                        </div>
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div> <!-- end container-fluid -->
@endsection

@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
@endpush
