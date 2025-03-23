@extends('admin.layouts.layout')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-dark">
                            <i class="mdi mdi-arrow-left-thin"></i>
                            Back
                        </a>
                    </div>
                    <h4 class="page-title">Thêm tài khoản</h4>
                </div>
            </div>
        </div>
        <div>
            <form id="blog-form" action="{{ route('admin.users.store') }}" method="post" enctype="multipart/form-data"
                class="form">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h4 class="header-title" style="margin-bottom: -20px">Thông tin cá nhân</h4>
                            </div>
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">Tên người dùng :</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Nhập tên người dùng" value="">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="email" class="font-weight-bold">Email :</label>
                                            <input type="text" class="form-control" id="email" name="email"
                                                placeholder="Nhập email" value="">
                                            @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="phone" class="font-weight-bold">Số điện thoại :</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                placeholder="Nhập số điện thoại" value="">
                                            @error('phone')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="font-weight-bold">Mật khẩu :</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Nhập mật khẩu" value="">
                                        @error('password')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- addresses Selection -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h4 class="header-title" style="margin-bottom: -20px">Địa chỉ</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Chọn địa chỉ:</label>

                                    <select name="name[]" id="tag-select" class="select2 form-control select2-multiple"
                                        data-toggle="select2" multiple="multiple" data-placeholder="Choose ...">
                                        {{-- @foreach ($tags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                        @endforeach --}}
                                    </select>

                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label for="new_tag_name" class="font-weight-bold">Thêm địa chỉ mới:</label>

                                    <div id="add-tag-section" style="display: none;">
                                        <input type="text" class="form-control mb-2" id="new_tag_name"
                                            name="new_tag_name" placeholder="Tên địa chỉ mới">

                                        <div class="d-flex justify-content-between col-12">
                                            <button type="button" id="confirm_add_tag"
                                                class="btn btn-primary mb-2 col-6">Xác
                                                nhận thêm</button>
                                            <button type="button" id="cancel_add_tag" class="btn btn-danger mb-2 col-5">Hủy
                                                thêm</button>
                                        </div>
                                    </div>

                                    <button type="button" id="show_add_tag" class="btn btn-link">+ Add new
                                        address</button>

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-4">
                        {{-- Phân quyền --}}
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h4 class="header-title" style="margin-bottom: -20px">Phân quyền</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Chọn quyền hạn:</label>
                                    <select class="form-control" id="category_select" name="category_id">
                                        {{-- <option value="">Chọn danh mục</option> --}}
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="form-group mt-3">
                                    <label for="new_role_name" class="font-weight-bold">Thêm quyền mới :</label>
                                    <div id="add-role-section" style="display: none;">
                                        <input type="text" class="form-control mb-2" id="new_role_name"
                                            name="new_role_name" placeholder="Tên quyền mới">
                                        <div class="d-flex justify-content-between col-12">
                                            <button type="button" id="confirm_add_rolerole"
                                                class="btn btn-primary mb-2 col-6">Xác nhận
                                                thêm</button>
                                            <button type="button" id="cancel_add_role"
                                                class="btn btn-danger mb-2 col-5">Hủy
                                                thêm</button>
                                        </div>
                                    </div>

                                    <button type="button" id="show_add_role" class="btn btn-link">+ Add new role</button>
                                </div>
                            </div>
                        </div>
                        <!-- User Image -->
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
                    <button type="submit" class="btn btn-success mr-2">Thêm tài khoản</button>
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

    <script src="{{ asset('admin/api/user.js') }}"></script>

@endpush
