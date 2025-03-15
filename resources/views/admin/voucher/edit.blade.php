@extends('admin.layouts.layout')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sửa Voucher</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.vouchers.editPutVoucher', $voucher->id) }}" method="post" class="form">
                @csrf
                @method('PUT')

                <!-- Tên Voucher -->
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Voucher</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên voucher..." value="{{  $voucher->name }}">
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Mã Voucher -->
                <div class="mb-3">
                    <label for="code" class="form-label">Mã Voucher</label>
                    <input type="text" name="code" class="form-control" placeholder="Nhập mã voucher..." value="{{ $voucher->code }}">
                    @error('code') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Mô tả -->
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả Voucher</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Nhập mô tả voucher...">{{  $voucher->description }}</textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Giá trị giảm giá -->
                <div class="mb-3">
                    <label for="discount_amount" class="form-label">Giá trị giảm giá</label>
                    <input type="number" step="0.01" name="discount_amount" class="form-control" placeholder="Nhập giá trị giảm..." value="{{ $voucher->discount_amount }}">
                    @error('discount_amount') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Loại Voucher -->
                <div class="mb-3">
                    <label for="type" class="form-label">Loại giảm giá</label>
                    <select name="type" class="form-select">
                        <option value="percentage" {{ old('type', $voucher->type) == 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                        <option value="fixed" {{ old('type', $voucher->type) == 'fixed' ? 'selected' : '' }}>Giá cố định</option>
                    </select>
                    @error('type') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Số lượng Voucher -->
                <div class="mb-3">
                    <label for="quantity" class="form-label">Số lượng</label>
                    <input type="number" name="quantity" class="form-control" placeholder="Nhập số lượng..." value="{{  $voucher->quantity }}">
                    @error('quantity') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Số lần tối đa mỗi user có thể sử dụng -->
                <div class="mb-3">
                    <label for="user_limit" class="form-label">Số lần tối đa cho mỗi user</label>
                    <input type="number" name="user_limit" class="form-control" placeholder="Nhập số lần..." value="{{  $voucher->user_limit }}">
                    @error('user_limit') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <!-- Thời gian bắt đầu -->
                <div class="mb-3">
                    <label for="start_date" class="form-label">Thời gian bắt đầu</label>
                    <input type="datetime-local" name="start_date" class="form-control" value="{{  $voucher->start_date }}">
                    @error('start_date') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Thời gian kết thúc -->
                <div class="mb-3">
                    <label for="end_date" class="form-label">Thời gian kết thúc</label>
                    <input type="datetime-local" name="end_date" class="form-control" value="{{  $voucher->end_date }}">
                    @error('end_date') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                   <!-- Số tiền tối thiểu -->
                   <div class="mb-3">
                    <label for="min_amount" class="form-label">Số tiền tối thiểu</label>
                    <input type="number" step="0.01" name="min_amount" class="form-control" placeholder="Nhập số tiền tối thiểu..." value="{{ $voucher->min_amount }}">
                    @error('min_amount')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Giá trị giảm giá tối đa -->
                <div class="mb-3">
                    <label for="max_discount_amount" class="form-label">Giá trị giảm giá tối đa</label>
                    <input type="number" step="0.01" name="max_discount_amount" class="form-control" placeholder="Nhập giá trị tối đa..." value="{{ $voucher->max_discount_amount }}">
                    @error('max_discount_amount')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Trạng thái -->
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', $voucher->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ old('status', $voucher->status) == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                    @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Phần Áp dụng -->
        <div class="mb-3">
            <label class="form-label">Áp dụng</label>
            <div class="row">
                <!-- Cột chọn Danh mục -->
                <div class="col-md-6">
                    <label for="applies_to_category" class="form-label">Danh mục</label>
                    <select name="applies_to[]" id="applies_to_category" class="form-control select2" multiple>
                        @foreach($categories as $category)
                            @php
                                // Lấy giá trị đã chọn: dùng old nếu có, ngược lại dùng voucher->applies_to (đã là mảng)
                                $selectedValues = old('applies_to', $voucher->applies_to);
                            @endphp
                            <option value="category_{{ $category->id }}"
                                {{ is_array($selectedValues) && in_array("category_{$category->id}", $selectedValues) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Cột chọn Sản phẩm -->
                <div class="col-md-6">
                    <label for="applies_to_product" class="form-label">Sản phẩm</label>
                    <select name="applies_to[]" id="applies_to_product" class="form-control select2" multiple>
                        @foreach($products as $product)
                            @php
                                $selectedValues = old('applies_to', $voucher->applies_to);
                            @endphp
                            <option value="product_{{ $product->id }}"
                                {{ is_array($selectedValues) && in_array("product_{$product->id}", $selectedValues) ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @error('applies_to')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

                <!-- Nút Xác nhận và Quay lại -->
                <div class="d-flex">
                    <button type="submit" class="btn btn-success me-2">Xác nhận</button>
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
