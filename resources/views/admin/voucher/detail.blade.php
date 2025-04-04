@extends('admin.layouts.layout')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết Voucher</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form class="form">

                <!-- Tên Voucher -->
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Voucher</label>
                    <input type="text" name="name" class="form-control" value="{{ $voucher->name }}" disabled>
                </div>

                <!-- Mã Voucher -->
                <div class="mb-3">
                    <label for="code" class="form-label">Mã Voucher</label>
                    <input type="text" name="code" class="form-control" value="{{ $voucher->code }}" disabled>
                </div>

                <!-- Mô tả -->
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả Voucher</label>
                    <textarea name="description" class="form-control" rows="3" disabled>{{ $voucher->description }}</textarea>
                </div>

                <!-- Giá trị giảm giá -->
                <div class="mb-3">
                    <label for="discount_amount" class="form-label">Giá trị giảm giá</label>
                    <input type="number" name="discount_amount" class="form-control" value="{{ $voucher->discount_amount }}" disabled>
                </div>

                <!-- Loại Voucher -->
                <div class="mb-3">
                    <label for="type" class="form-label">Loại giảm giá</label>
                    <select name="type" class="form-select" disabled>
                        <option value="percentage" {{ $voucher->type == 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                        <option value="fixed" {{ $voucher->type == 'fixed' ? 'selected' : '' }}>Giá cố định</option>
                    </select>
                </div>

                <!-- Số lượng -->
                <div class="mb-3">
                    <label for="quantity" class="form-label">Số lượng</label>
                    <input type="number" name="quantity" class="form-control" value="{{ $voucher->quantity }}" disabled>
                </div>

                <!-- Thời gian bắt đầu -->
                <div class="mb-3">
                    <label for="start_date" class="form-label">Thời gian bắt đầu</label>
                    <input type="datetime-local" name="start_date" class="form-control" value="{{ $voucher->start_date }}" disabled>
                </div>

                <!-- Thời gian kết thúc -->
                <div class="mb-3">
                    <label for="end_date" class="form-label">Thời gian kết thúc</label>
                    <input type="datetime-local" name="end_date" class="form-control" value="{{ $voucher->end_date }}" disabled>
                </div>
                <!-- Số tiền tối thiểu -->
                <div class="mb-3">
                    <label for="min_amount" class="form-label">Số tiền tối thiểu</label>
                    <input type="number" step="0.01" name="min_amount" class="form-control" placeholder="Nhập số tiền tối thiểu..." value="{{ $voucher->min_amount }}" disabled>
                </div>

                <!-- Giá trị giảm giá tối đa -->
                <div class="mb-3">
                    <label for="max_discount_amount" class="form-label">Giá trị giảm giá tối đa</label>
                    <input type="number" step="0.01" name="max_discount_amount" class="form-control" placeholder="Nhập giá trị tối đa..." value="{{ $voucher->max_discount_amount }}" disabled>
                </div>

                <!-- Trạng thái -->
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" class="form-select" disabled>
                        <option value="active" {{ $voucher->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ $voucher->status == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>

                <!-- Danh mục & Sản phẩm -->
                <div class="mb-3">
                    <label class="form-label">Áp dụng cho</label>
                    <div class="row">
                        <!-- Danh mục -->
                        <div class="col-md-6">
                            <label class="form-label">Danh mục</label>
                            <select class="form-control select2" multiple disabled>
                                @foreach($categories as $category)
                                <option value="category_{{ $category->id }}" {{ in_array("category_{$category->id}", $voucher->applies_to) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sản phẩm -->
                        <div class="col-md-6">
                            <label class="form-label">Sản phẩm</label>
                            <select class="form-control select2" multiple disabled>
                                @foreach($products as $product)
                                <option value="product_{{ $product->id }}" {{ in_array("product_{$product->id}", $voucher->applies_to) ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Nút quay lại -->
                <div class="d-flex">
                    <a href="{{ route('admin.vouchers.listVoucher') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<x-admin.data-table-styles />
@endpush

@push('scripts')
<x-admin.data-table-scripts />
@endpush