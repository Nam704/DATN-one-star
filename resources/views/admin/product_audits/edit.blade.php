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
                        <form action="{{ route('admin.product_audits.update', $audit->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Người dùng -->
                            <div class="mb-3">
                                <label for="id_user" class="form-label">Người dùng</label>
                                <select name="id_user" id="id_user" class="form-select">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ $audit->id_user == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
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
                                    @foreach ($productVariants as $variant)
                                        <option value="{{ $variant->id }}" {{ $audit->id_product_variant == $variant->id ? 'selected' : '' }}>
                                            {{ $variant->sku }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_product_variant')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Số lượng -->
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Số lượng</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $audit->quantity }}">
                                @error('quantity')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Loại hành động -->
                            <div class="mb-3">
                                <label for="action_type" class="form-label">Loại hành động</label>
                                <select name="action_type" id="action_type" class="form-select">
                                    <option value="add" {{ $audit->action_type == 'add' ? 'selected' : '' }}>Thêm</option>
                                    <option value="remove" {{ $audit->action_type == 'remove' ? 'selected' : '' }}>Xóa</option>
                                    <option value="update" {{ $audit->action_type == 'update' ? 'selected' : '' }}>Cập nhật</option>
                                </select>
                                @error('action_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Lý do -->
                            <div class="mb-3">
                                <label for="reason" class="form-label">Lý do</label>
                                <textarea name="reason" id="reason" class="form-control">{{ $audit->reason }}</textarea>
                                @error('reason')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Nút gửi -->
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
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
