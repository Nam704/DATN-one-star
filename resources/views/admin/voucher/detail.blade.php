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

                <!-- Voucher Name and Code -->
                <div class=" row">
                    <div class="col-md-6 mb-4">
                        <label for="name" class="form-label text-dark">Tên Voucher</label>
                        <input type="text" name="name" class="form-control form-control-lg shadow-sm" placeholder="Nhập tên voucher..." value="{{ $voucher->name }}" disabled>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="code" class="form-label text-dark">Mã Voucher</label>
                        <input type="text" name="code" class="form-control form-control-lg shadow-sm" placeholder="Nhập mã voucher..." value="{{ $voucher->code }}" disabled>
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="mb-4">
                    <label for="description" class="form-label text-dark">Mô tả Voucher</label>
                    <textarea name="description" class="form-control form-control-lg shadow-sm" rows="3" disabled>{{ $voucher->description }}</textarea>
                </div>

                     <!-- Discount Amount and Type -->
                     <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="discount_amount" class="form-label text-dark">Giá trị giảm giá</label>
                        <input type="number" step="0.01" name="discount_amount" class="form-control form-control-lg shadow-sm" placeholder="Nhập giá trị giảm..." value="{{ $voucher->discount_amount }}" disabled>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="type" class="form-label text-dark">Loại giảm giá</label>
                        <select name="type" class="form-select form-control-lg shadow-sm" disabled>
                            <option value="percentage" {{ old('type', $voucher->type) == 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                            <option value="fixed" {{ old('type', $voucher->type) == 'fixed' ? 'selected' : '' }}>Giá cố định</option>
                        </select>
                    </div>
                </div>

                <!-- Quantity and User Limit -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="quantity" class="form-label text-dark">Số lượng</label>
                        <input type="number" name="quantity" class="form-control form-control-lg shadow-sm" placeholder="Nhập số lượng..." value="{{ $voucher->quantity }}" disabled>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="user_limit" class="form-label text-dark">Số lần tối đa cho mỗi user</label>
                        <input type="number" name="user_limit" class="form-control form-control-lg shadow-sm" placeholder="Nhập số lần..." value="{{ $voucher->user_limit }}" disabled>
                    </div>
                </div>

       <!-- Start and End Dates -->
       <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="start_date" class="form-label text-dark">Thời gian bắt đầu</label>
                        <input type="datetime-local" name="start_date" class="form-control form-control-lg shadow-sm" value="{{ $voucher->start_date }}" disabled>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="end_date" class="form-label">Thời gian kết thúc</label>
                        <input type="datetime-local" name="end_date" class="form-control form-control-lg shadow-sm" value="{{ $voucher->end_date }}" disabled>
                    </div>
                </div>

               <!-- Min and Max Discount -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="min_amount" class="form-label text-dark">Số tiền tối thiểu</label>
                        <input type="number" step="0.01" name="min_amount" class="form-control form-control-lg shadow-sm" placeholder="Nhập số tiền tối thiểu..." value="{{ $voucher->min_amount }}" disabled>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="max_discount_amount" class="form-label text-dark">Giá trị giảm giá tối đa</label>
                        <input type="number" step="0.01" name="max_discount_amount" class="form-control form-control-lg shadow-sm" placeholder="Nhập giá trị tối đa..." value="{{ $voucher->max_discount_amount }}" disabled>
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label for="status" class="form-label text-dark">Trạng thái</label>
                    <select name="status" class="form-select form-control-lg shadow-sm" disabled>
                        <option value="active" {{ old('status', $voucher->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ old('status', $voucher->status) == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>

                  <!-- Applies To Categories and Products -->
                  <div class="mb-4">
                    <label class="form-label text-dark">Áp dụng cho</label>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="applies_to_category" class="form-label text-dark">Danh mục</label>
                            <select name="applies_to[]" id="applies_to_category" class="form-control form-control-lg select2" multiple disabled>
                                @foreach($categories as $category)
                                    @php
                                        $selectedValues = old('applies_to', $voucher->applies_to);
                                    @endphp
                                    <option value="category_{{ $category->id }}"
                                        {{ is_array($selectedValues) && in_array("category_{$category->id}", $selectedValues) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="applies_to_product" class="form-label text-dark">Sản phẩm</label>
                            <select name="applies_to[]" id="applies_to_product" class="form-control form-control-lg select2" multiple disabled>
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
                </div>

                <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.vouchers.listVoucher') }}" class="btn btn-outline-primary py-2 px-4 shadow-sm rounded">Quay lại</a>
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