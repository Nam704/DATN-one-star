@extends('admin.layouts.layout')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 mt-2">Thêm mới sản phẩm</h1>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('admin.products.addPostProduct') }}" method="post" enctype="multipart/form-data"
                    class="form">
                    @csrf
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
                            <div class="mb-3">
                                <label for="id_brand" class="form-label">Brand:</label>
                                <select class="form-select" id="id_brand" name="id_brand" required>
                                    <option value="" selected disabled>Select a brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ old('id_brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a brand.</div>
                            </div>

                            <div class="mb-3">
                                <label for="image_primary" class="form-label">Primary Image:</label>
                                <input class="form-control" type="file" id="image_primary" name="image_primary" required>
                                <div class="invalid-feedback">Please upload a primary image.</div>
                            </div>

                            <div class="form-group">
                                <label for="description" class="font-weight-bold">Mô tả sản phẩm:</label>
                                <textarea id="description" name="description" class="form-control"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="status">Trạng thái</label>
                                <select name="status" class="form-control" required>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                        </div>
                    </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Danh mục sản phẩm</h6>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label class="font-weight-bold">Chọn danh mục:</label>
                            <div class="border p-2 rounded">
                                @foreach ($categories as $category)
                                    @if ($category->id_parent == null)
                                        <div class="category-item" id="category_{{ $category->id }}">
                                            <label class="toggle-subcategories" data-category-id="{{ $category->id }}">
                                                <input type="checkbox" class="form-check-input" name="id_category[]"
                                                    value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                            <!-- Danh mục con (Subcategories) -->
                                            <div class="subcategory-container" id="subcategories_{{ $category->id }}"
                                                style="display: none;">
                                                @foreach ($categories as $child)
                                                    @if ($child->id_parent == $category->id)
                                                        <div class="category-item" id="category_{{ $child->id }}">
                                                            <label class="toggle-subcategories"
                                                                data-category-id="{{ $child->id }}">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="id_category[]" value="{{ $child->id }}">
                                                                {{ $child->name }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label for="new_category_name" class="font-weight-bold">Thêm danh mục mới:</label>
                            <div id="add-category-section">
                                <input type="text" class="form-control mb-2" id="new_category_name"
                                    name="new_category_name" placeholder="Tên danh mục mới" style="display: none;">
                                <select class="form-control mb-2" id="id_parent" name="id_parent" style="display: none;">
                                    <option value="">-- Chọn danh mục cha --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" id="confirm_add_category" class="btn btn-primary mb-2"
                                    style="display: none;">Xác nhận thêm</button>
                            </div>
                            <button type="button" id="show_add_category" class="btn btn-link">+ Add new category</button>
                        </div>

                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU:</label>
                                <input type="text" class="form-control" name="variants[0][sku]" required>
                                <div class="invalid-feedback">Please enter the SKU.</div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status:</label>
                                <select class="form-select" name="variants[0][status]">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Variant Images (max 4):</label>
                                <div id="variant-images-container-0">
                                    <!-- Images will be added dynamically here -->
                                </div>
                                <button type="button" class="btn btn-outline-secondary add-image-button"
                                    data-variant-index="0">Add Image</button>
                                <small class="text-muted d-block mt-2">You can upload up to 4 images for this
                                    variant.</small>
                                <div class="text-danger image-error" style="display: none;">You can only upload up to 4
                                    images.</div>
                            </div>
                        </div>
                    </div>

                </div>


                {{-- <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Thêm Ảnh Phụ</h6>
                    </div>
                    <div class="card-body">
                        <!-- Add New Image Section -->
                        <div class="mb-4">
                            <h3 class="mb-3">Additional Images</h3>
                            <button type="button" class="btn btn-outline-secondary mb-3" onclick="addImage()">Add
                                Image</button>
                            <div id="additional-images"></div>
                        </div>
                    </div>
                </div> --}}
            </div>



            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success mr-2">Thêm sản phẩm</button>
                <a href="{{ route('admin.products.listProduct') }}" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
            </form>
        </div>

    </div>
@endsection
@push('styles')
    <x-admin.data-table-styles />
    <!-- <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet"> -->
    <link href="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.css" rel="stylesheet">
    <style>
        .ql-toolbar {
            border-radius: 5px 5px 0 0;
            border: 1px solid #ddd;
        }

        .ql-container {
            border-radius: 0 0 5px 5px;
            border: 1px solid #ddd;
        }

        .subcategory-container {
            margin-left: 20px;

        }

        .subcategory-container .category-item {
            margin-left: 20px;

        }

        .category-item {
            margin-bottom: 10px;

        }
    </style>
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
    <!-- <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.0/tinymce.min.js"></script>
    <script>
        document.querySelectorAll('.add-image-button').forEach(function(button) {
            button.addEventListener('click', function() {
                const variantIndex = this.getAttribute('data-variant-index');
                const container = document.getElementById(`variant-images-container-${variantIndex}`);
                const imageCount = container.querySelectorAll('input[type="file"]').length;

                if (imageCount >= 4) {
                    this.nextElementSibling.style.display = 'block'; // Hiển thị lỗi
                    return;
                }

                this.nextElementSibling.style.display = 'none';

                const input = document.createElement('input');
                input.type = 'file';
                input.name = `variants[${variantIndex}][images][]`;
                input.className = 'form-control mt-2';
                input.accept = 'image/*';

                container.appendChild(input);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#description',
                plugins: 'lists link image table code',
                toolbar: 'bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | table | code',
                height: 300,
                forced_root_block: '',
                placeholder: 'Nhập nội dung sản phẩm tại đây...',
                setup: function(editor) {

                    editor.on('change', function() {
                        tinymce.triggerSave();
                    });
                },
                init_instance_callback: function(editor) {
                    var oldDescription = "{{ old('description') }}";
                    if (oldDescription) {
                        editor.setContent(oldDescription);
                    }
                }
            });

        });

        document.querySelectorAll('.toggle-subcategories').forEach(function(label) {
            label.addEventListener('click', function(event) {
                event.preventDefault();
                var categoryId = this.getAttribute('data-category-id');
                var checkbox = document.querySelector('#category_' + categoryId +
                    ' input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                var subcategoryContainer = document.getElementById('subcategories_' + categoryId);
                subcategoryContainer.style.display = subcategoryContainer.style.display === 'none' ?
                    'block' : 'none';
            });
        });;
        document.querySelector('form').addEventListener('submit', function(e) {
            const editorContent = quill.root.innerHTML;
            document.getElementById('description-input').value = editorContent;
        });

        document.getElementById('show_add_category').addEventListener('click', function() {
            document.getElementById('new_category_name').style.display = 'block';
            document.getElementById('id_parent').style.display = 'block';
            document.getElementById('confirm_add_category').style.display = 'block';
            this.style.display = 'none';
        });

        document.getElementById('confirm_add_category').addEventListener('click', function() {
            const newCategoryName = document.getElementById('new_category_name').value.trim();
            const parentCategory = document.getElementById('id_parent').value;

            if (!newCategoryName) {
                alert('Vui lòng nhập tên danh mục!');
                return;
            }

            $.ajax({
                type: "POST",
                url: "{{ route('admin.categories.addCategoryProduct') }}",
                data: {
                    name: newCategoryName,
                    id_parent: parentCategory,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                    }
                },
                error: function(xhr) {
                    alert('Lỗi khi gửi dữ liệu: ' + xhr.responseText);
                }
            });
        });
    </script>
@endpush
