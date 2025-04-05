@extends('admin.layouts.layout')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 mt-4">Thêm Slides</h4>
        </div>
        <div>
            <form id="blog-form" action="{{ route('admin.slides.store') }}" method="post" enctype="multipart/form-data"
                class="form">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title" class="font-weight-bold">Tiêu đề :</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Nhập tiêu đều bài viết" value="{{ old('title') }}">
                                    @error('title')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="content" class="font-weight-bold">Nội dung bài viết:</label>

                                    <!-- Quill editor -->
                                    <div id="snow-editor" style="height: 300px; background: #fff;"></div>
                                    <!-- Input hidden để lưu nội dung -->
                                    <input type="hidden" name="content" id="content">
                                    @error('content')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="col-lg-4">
                        {{-- Danh mục --}}
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h4 class="header-title" style="margin-bottom: -20px">Danh mục bài viết</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Chọn danh mục:</label>
                                    <select class="form-control" id="category_select" name="category_id">
                                        {{-- <option value="">Chọn danh mục</option> --}}
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="form-group mt-3">
                                    <label for="new_category_name" class="font-weight-bold">Thêm danh mục mới:</label>

                                    <div id="add-category-section" style="display: none;">
                                        <input type="text" class="form-control mb-2" id="new_category_name"
                                            name="new_category_name" placeholder="Tên danh mục mới">
                                        <div class="d-flex justify-content-between col-12">
                                            <button type="button" id="confirm_add_category"
                                                class="btn btn-primary mb-2 col-6">Xác nhận
                                                thêm</button>
                                            <button type="button" id="cancel_add_category"
                                                class="btn btn-danger mb-2 col-5">Hủy
                                                thêm</button>
                                        </div>
                                    </div>

                                    <button type="button" id="show_add_category" class="btn btn-link">+ Add new
                                        category</button>
                                </div>
                            </div>
                        </div>

                        <!-- Product Image -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h4 class="m-0 font-weight-bold text-primary">Ảnh chính</h4>
                            </div>
                            <div class="card-body">
                                <input name="image_primary" type="file" id="slideImage" class="form-control mb-3"
                                    accept="image/*">
                                <div id="imagePreview" class="text-center"></div>
                            </div>
                        </div>

                        <!-- Product Album -->
                        <div class="card shadow mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="m-0 font-weight-bold text-primary">Slides Image</h4>
                                <button type="button" id="addAlbumImage" class="btn btn-primary btn-sm">+</button>
                            </div>
                            <div class="card-body">
                                <input type="file" id="albumImages" name="images[]" class="form-control mb-3"
                                    accept="image/*" multiple style="display: none;">
                                <div id="albumPreview" class="row g-2"></div>
                            </div>
                        </div>

                        <!-- Product Album -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h4 class="m-0 font-weight-bold text-primary">Vị trí hiển thị</h4>
                            </div>
                            <div class="card-body">
                                <label><input type="checkbox" name="display_locations[]" value="home"> Trang chủ</label>
                                <label><input type="checkbox" name="display_locations[]" value="banner"> Trang sản phẩm</label>
                                <label><input type="checkbox" name="display_locations[]" value="product"> Danh mục</label>
                                @error('display_position')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success mr-2">Thêm slides</button>
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
    <!-- Quill Editor js -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <!-- Quill Demo js -->
    <script src="{{ asset('admin/assets/js/pages/quilljs.init.js') }}"></script>

    <script src="{{ asset('admin/api/slides.js') }}"></script>
@endpush
