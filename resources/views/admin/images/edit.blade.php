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
                        <form action="{{ route('admin.images.update', $image->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="url">Chọn ảnh mới (Nếu có)</label>
                                <input type="file" class="form-control" name="url">
                            </div>

                            <div class="form-group">
                                <label for="id_product_variant">Sản phẩm</label>
                                <select name="id_product_variant" class="form-control" required>
                                    @foreach ($productVariants as $variant)
                                        <option value="{{ $variant->id }}" {{ $image->id_product_variant == $variant->id ? 'selected' : '' }}>
                                            {{ $variant->sku }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="status">Trạng thái</label>
                                <select name="status" class="form-control" required>
                                    <option value="active" {{ $image->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ $image->status == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                                </select>
                            </div>

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
