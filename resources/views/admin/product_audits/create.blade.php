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
                        <form action="{{ route('admin.product_audits.store') }}" method="POST">
                            @csrf

                            <!-- Người dùng -->
                            <div class="mb-3">
                                <label for="id_user" class="form-label">Người dùng</label>
                                <select name="id_user" id="id_user" class="form-select">
                                    <option value="" disabled selected>Chọn người dùng</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('id_user')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Biến thể sản phẩm -->
                            <div class="mb-3">
                                <label for="id_product_variant" class="form-label">Biến thể sản phẩm</label>
                                <select name="id_product_variant" id="id_product_variant" class="form-select">
                                    <option value="" disabled selected>Chọn biến thể sản phẩm</option>
                                    @foreach ($productVariants as $variant)
                                        <option value="{{ $variant->id }}">{{ $variant->sku }}</option>
                                    @endforeach
                                </select>
                                @error('id_product_variant')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Số lượng -->
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Số lượng</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity') }}">
                                @error('quantity')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Loại hành động -->
                            <div class="mb-3">
                                <label for="action_type" class="form-label">Loại hành động</label>
                                <select name="action_type" id="action_type" class="form-select">
                                    <option value="" disabled selected>Chọn loại hành động</option>
                                    <option value="add">Thêm</option>
                                    <option value="remove">Xóa</option>
                                    <option value="update">Cập nhật</option>
                                </select>
                                @error('action_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Lý do -->
                            <div class="mb-3">
                                <label for="reason" class="form-label">Lý do</label>
                                <textarea name="reason" id="reason" class="form-control">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Nút gửi -->
                            <button type="submit" class="btn btn-primary">Lưu</button>
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
