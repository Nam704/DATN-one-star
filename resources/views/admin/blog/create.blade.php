@extends('admin.layouts.layout')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-dark">
                        <i class="mdi mdi-arrow-left-thin"></i>
                        Back
                    </a>
                </div>
                <h4 class="page-title">Add Blog</h4>
            </div>
        </div>
        <div>
            <form id="blog-form" action="{{ route('admin.blogs.store') }}" method="post" enctype="multipart/form-data"
                class="form">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title" class="font-weight-bold">Tên bài viết:</label>
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
                                        @foreach ($categoryBlog as $category)
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

                        <!-- Tag Selection -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h4 class="header-title" style="margin-bottom: -20px">Thẻ tag</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Chọn thẻ tag:</label>

                                    @foreach ($tags as $tag)
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="name[]"
                                                id="tag-{{ $tag->id }}" value="{{ $tag->id }}">
                                            <label class="form-check-label"
                                                for="tag-{{ $tag->id }}">{{ $tag->name }}</label>
                                        </div>
                                    @endforeach

                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label for="new_tag_name" class="font-weight-bold">Thêm thẻ tag mới:</label>

                                    <div id="add-tag-section" style="display: none;">
                                        <input type="text" class="form-control mb-2" id="new_tag_name"
                                            name="new_tag_name" placeholder="Tên thẻ tag mới">

                                        <div class="d-flex justify-content-between col-12">
                                            <button type="button" id="confirm_add_tag"
                                                class="btn btn-primary mb-2 col-6">Xác
                                                nhận thêm</button>
                                            <button type="button" id="cancel_add_tag"
                                                class="btn btn-danger mb-2 col-5">Hủy
                                                thêm</button>
                                        </div>
                                    </div>

                                    <button type="button" id="show_add_tag" class="btn btn-link">+ Add new
                                        tag</button>

                                </div>
                            </div>
                        </div>

                        <!-- Product Image -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h4 class="header-title" style="margin-bottom: -20px">Ảnh bài viết</h4>
                            </div>
                            <div class="card-body">
                                <input name="thumbnail" type="file" id="blogImage" class="form-control mb-3"
                                    accept="image/*">
                                <div id="imagePreview" class="text-center"></div>
                            </div>
                        </div>
                        <input type="hidden" name="status" id="status" value="published">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning mr-2" onclick="setStatus('draft')">Bản nháp</button>
                    <button type="submit" class="btn btn-success mr-2" onclick="setStatus('published')">Thêm bài
                        viết</button>
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

    <script src="{{ asset('admin/api/blog.js') }}"></script>

    <script src="{{ asset('admin/api/testFunction.js') }}"></script>
@endpush
