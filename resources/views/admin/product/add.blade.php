@extends('admin.layouts.layout')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 mt-2">Thêm mới sản phẩm</h1>
    </div>
    <div>
        <form id="product-form" action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data"
            class="form">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Tên sản phẩm:</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Nhập tên sản phẩm" value="{{ old('name') }}">
                                @error('name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description" class="font-weight-bold">Mô tả sản phẩm:</label>

                                <!-- Quill editor -->
                                <div id="snow-editor" style="height: 300px; background: #fff;"></div>
                                <!-- Input hidden để lưu nội dung -->
                                <input type="hidden" name="description" id="description">

                            </div>

                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3 p-2">
                            <label class="fw-bold mb-0">Dữ liệu sản phẩm —</label>
                            <select class="form-select w-auto" id="productType">

                                <option value="variants" selected>Sản phẩm có biến thể</option>
                            </select>
                            <button class="btn btn-link p-0" type="button" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Thông tin">
                                <i class="bi bi-question-circle"></i>
                            </button>
                        </div>

                        <div class="container-fluid">
                            <div class="row">
                                <!-- Sidebar -->
                                <div class="col-md-3 col-lg-2 border-end">
                                    <div class="list-group list-group-flush">

                                        <a href="#" class="list-group-item list-group-item-action" id="attributes-tab"
                                            data-bs-toggle="tab" data-bs-target="#attributes">
                                            <i class="bi bi-tags me-2"></i>Các thuộc tính
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action" id="variations-tab"
                                            data-bs-toggle="tab" data-bs-target="#variations">
                                            <i class="bi bi-sliders me-2"></i>Các biến thể
                                        </a>

                                    </div>
                                </div>

                                <!-- Main Content -->
                                <div class="col-md-9 col-lg-10">
                                    <div class="tab-content" id="productTabsContent">
                                        <!-- Các thuộc tính Tab -->
                                        <div class="tab-pane fade" id="attributes" role="tabpanel">
                                            <div class="container" id="attribute-content">
                                                <div class="row mb-2">

                                                    <div class="col-7">
                                                        <select id="attributeSelect" class="form-select">
                                                            <option value="" selected>Thêm hiện có</option>
                                                            @foreach ($attributes as $item)

                                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>


                                                    <div class="col-4">
                                                        <button type="button" id="add-new-attribute"
                                                            class="btn btn-primary w-100">
                                                            Thêm mới
                                                        </button>

                                                    </div>
                                                    <div class="col-11">
                                                        <div id="add-attribute-section" class="mt-2"
                                                            style="display: none ;">
                                                            <input type="text" class="form-control mb-2"
                                                                id="new_attribute_name" name="new_attribute_name"
                                                                placeholder="Tên hãng mới">

                                                            <div class="d-flex justify-content-between col-12">
                                                                <button type="button" id="confirm_add_attribute"
                                                                    class="btn btn-primary mb-2 col-6">Xác
                                                                    nhận thêm</button>
                                                                <button type="button" id="cancel_add_attribute"
                                                                    class="btn btn-danger mb-2 col-5">Hủy
                                                                    thêm</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="new-attribute-row-container">

                                                </div>


                                                <div class="d-flex justify-content-end mt-3">
                                                    <button type="button" id="save-attribute-btn"
                                                        class="btn btn-primary">
                                                        Lưu chọn thuộc tính
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Các biến thể Tab -->
                                        <div class="tab-pane fade" id="variations" role="tabpanel">

                                            <div class="button-container">
                                                <button type="button" id="generate-variants"
                                                    class="btn btn-outline-primary mb-2 mr-2" style="margin-right: 7px">
                                                    Tạo tự động
                                                </button>
                                                <button type="button" id="add-new-var"
                                                    class="btn btn-outline-primary mb-2" style="margin-right: 7px">
                                                    Thêm mới biến thể
                                                </button>
                                                <button type="button" id="save-var" class="btn btn-primary mb-2">
                                                    Lưu Biến thể
                                                </button>
                                            </div>

                                            <div id="container-variations"></div>

                                            <div id="data-vars"></div>

                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    {{-- Danh mục sản phẩm --}}
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h4 class="m-0 font-weight-bold text-primary">Danh mục sản phẩm</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Chọn danh mục:</label>
                                <select class="form-control" id="category_select" name="id_category">
                                    <option value="">Chọn danh mục</option>
                                    @foreach ($categories as $category)
                                    @if (!$category->id_parent)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @foreach ($categories as $sub)
                                    @if ($sub->id_parent == $category->id)
                                    <option value="{{ $sub->id }}">-- {{ $sub->name }}</option>
                                    @endif
                                    @endforeach
                                    @endif
                                    @endforeach
                                </select>
                                @error('id_category')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="form-group mt-3">
                                <label for="new_category_name" class="font-weight-bold">Thêm danh mục mới:</label>

                                <div id="add-category-section" style="display: none;">
                                    <input type="text" class="form-control mb-2" id="new_category_name"
                                        name="new_category_name" placeholder="Tên danh mục mới">
                                    <select class="form-control mb-2" id="id_parent" name="id_parent">
                                        <option value="">-- Chọn danh mục cha --</option>
                                        @foreach ($categories as $category)
                                        @if (!$category->id_parent)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>

                                        @endif
                                        @endforeach
                                    </select>
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
                    <!-- Product Brand -->
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h4 class="m-0 font-weight-bold text-primary">Hãng sản phẩm</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Chọn hãng:</label>

                                <select name="id_brand" class="form-control mb-2" id="brand-select">
                                    <option value="">Chọn hãng</option>
                                    @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>

                                @error('brand')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mt-3">
                                <label for="new_brand_name" class="font-weight-bold">Thêm hãng mới:</label>

                                <div id="add-brand-section" style="display: none;">
                                    <input type="text" class="form-control mb-2" id="new_brand_name"
                                        name="new_brand_name" placeholder="Tên hãng mới">

                                    <div class="d-flex justify-content-between col-12">
                                        <button type="button" id="confirm_add_brand"
                                            class="btn btn-primary mb-2 col-6">Xác
                                            nhận thêm</button>
                                        <button type="button" id="cancel_add_brand"
                                            class="btn btn-danger mb-2 col-5">Hủy
                                            thêm</button>
                                    </div>
                                </div>

                                <button type="button" id="show_add_brand" class="btn btn-link">+ Add new
                                    brand</button>

                            </div>
                        </div>
                    </div>
                    <!-- Product Image -->
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h4 class="m-0 font-weight-bold text-primary">Ảnh sản phẩm</h4>
                        </div>
                        <div class="card-body">
                            <input name="image_primary" type="file" id="productImage" class="form-control mb-3"
                                accept="image/*">
                            <div id="imagePreview" class="text-center"></div>
                        </div>
                    </div>

                    <!-- Product Album -->
                    <div class="card shadow mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="m-0 font-weight-bold text-primary">Album sản phẩm</h4>
                            <button type="button" id="addAlbumImage" class="btn btn-primary btn-sm">+</button>
                        </div>
                        <div class="card-body">
                            <input type="file" id="albumImages" name="images[]" class="form-control mb-3"
                                accept="image/*" multiple style="display: none;">
                            <div id="albumPreview" class="row g-2"></div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success mr-2">Thêm sản phẩm</button>
                {{-- <a href="route('admin.products.listProduct')" class="btn btn-secondary ms-2">Quay lại</a> --}}
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

<script src="{{ asset('admin/api/addProduct.js') }}"></script>

<script src="{{ asset('admin/api/testFunction.js') }}"></script>


@endpush