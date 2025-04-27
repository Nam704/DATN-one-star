@extends('admin.layouts.layout')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 mt-2">Thêm mới Banner</h1>
    </div>

    <div>
        <form id="banner-form" action="{{ route('admin.banner.update',$banner->id) }}" method="post" enctype="multipart/form-data" class="form">
            @csrf
            @method('PUT')
            <div class="row">
                <!-- Cột trái -->
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <!-- Tiêu đề -->
                            <div class="form-group">
                                <label for="title" class="font-weight-bold">Tiêu đề:</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="Tiêu đề" value="{{  $banner->title }}">
                                @error('title')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mô tả -->
                            <div class="form-group">
                    <label for="description" class="form-weight-bold">Mô tả:</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Nhập mô tả voucher...">{{ $banner->description }}</textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <!-- Ngày bắt đầu & kết thúc -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date" class="font-weight-bold">Ngày bắt đầu:</label>
                                        <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="{{ $banner->start_date }}">
                                        @error('start_date')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date" class="font-weight-bold">Ngày kết thúc:</label>
                                        <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="{{ $banner->end_date }}">
                                        @error('end_date')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải -->
                <div class="col-lg-4">
                    <!-- Trạng thái -->
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h4 class="m-0 font-weight-bold text-primary">Trạng thái</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                            <select class="form-control" name="status">
                                    <option value="1" {{ old('status', $banner->status) == '1' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="0" {{ old('status', $banner->status) == '0' ? 'selected' : '' }}>Không hoạt động</option>
                                </select>
                                @error('status')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Hình ảnh -->
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h4 class="m-0 font-weight-bold text-primary">Ảnh</h4>
                        </div>
                        <div class="card-body">
                            <input name="image" type="file" id="productImage" class="form-control mb-3"
                                accept="image/*">
                                @if($banner->image)
                                <div class="text-center">
                                    <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner Image" class="img-fluid">
                                </div>
                            @endif
                            @error('image')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-success">Cập nhật banner</button>
                        <a href="{{ route('admin.banner.list') }}" class="btn btn-secondary ml-2">Quay lại</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /.container-fluid -->
@endsection

@push('styles')
<!-- Quill css -->


<link href="{{ asset('admin/assets/vendor/quill/quill.core.css') }}" rel="stylesheet" type="text/css" />


<link href="{{ asset('admin/assets/vendor/quill/quill.snow.css') }}" rel="stylesheet" type="text/css" />

@endpush

@push('scripts')



@endpush