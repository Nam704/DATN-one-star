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
            <form action="{{route('admin.products.addPostProduct')}}" method="post" enctype="multipart/form-data" class="form">
                @csrf
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Tên sản phẩm:</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên sản phẩm" value="{{old('name')}}">
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
                                            <input type="checkbox" class="form-check-input" name="id_category[]" value="{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                        <!-- Danh mục con (Subcategories) -->
                                        <div class="subcategory-container" id="subcategories_{{ $category->id }}" style="display: none;">
                                            @foreach ($categories as $child)
                                                @if ($child->id_parent == $category->id)
                                                    <div class="category-item" id="category_{{ $child->id }}">
                                                        <label class="toggle-subcategories" data-category-id="{{ $child->id }}">
                                                            <input type="checkbox" class="form-check-input" name="id_category[]" value="{{ $child->id }}">
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
                            <input type="text" class="form-control mb-2" id="new_category_name" name="new_category_name" placeholder="Tên danh mục mới" style="display: none;">
                            <select class="form-control mb-2" id="id_parent" name="id_parent" style="display: none;">
                                <option value="">-- Chọn danh mục cha --</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="confirm_add_category" class="btn btn-primary mb-2" style="display: none;">Xác nhận thêm</button>
                        </div>
                        <button type="button" id="show_add_category" class="btn btn-link">+ Add new category</button>
                    </div>

                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="mb-4">
                    <h3 class="mb-3">Variants</h3>
                    <button type="button" class="btn btn-outline-secondary mb-3" onclick="addVariant()">Add Variant</button>
                    <div id="variants">
                        <!-- Default variant -->
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
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-danger" onclick="removeVariant(this)">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card shadow mb-4">
                
            </div>
        </div>



        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success mr-2">Thêm sản phẩm</button>
            <a href="{{ route('admin.products.listProduct')}}" class="btn btn-secondary ms-2">Quay lại</a>
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
            var checkbox = document.querySelector('#category_' + categoryId + ' input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            var subcategoryContainer = document.getElementById('subcategories_' + categoryId);
            subcategoryContainer.style.display = subcategoryContainer.style.display === 'none' ? 'block' : 'none';
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









{{-- @extends('admin.layouts.layout')
@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 mt-2">Thêm mới sản phẩm</h1>
        </div>

        <div class="row">

            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form action="{{ route('admin.products.addPostProduct') }}" method="post"
                            enctype="multipart/form-data" class="form">
                            @csrf

                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Tên sản phẩm:</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Nhập tên sản phẩm" required>
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
                                <div id="snow-editor" style="height: 300px; border: 1px solid #ddd; border-radius: 5px;">
                                </div>
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
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="category_{{ $category->id }}" name="id_category[]"
                                                value="{{ $category->id }}">
                                            <label class="form-check-label"
                                                for="category_{{ $category->id }}">{{ $category->name }}</label>
                                        </div>

                                        @foreach ($categories as $child)
                                            @if ($child->id_parent == $category->id)
                                                <div class="form-check ms-4">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="category_{{ $child->id }}" name="id_category[]"
                                                        value="{{ $child->id }}">
                                                    <label class="form-check-label"
                                                        for="category_{{ $child->id }}">{{ $child->name }}</label>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                            @error('id_category')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>






                        <!-- Add New Category Section -->
                        <div class="form-group mt-3">
                            <label for="new_category_name" class="font-weight-bold">Thêm danh mục mới:</label>
                            <div id="add-category-section">
                                <input type="text" class="form-control mb-2" id="new_category_name"
                                    placeholder="Tên danh mục mới">
                                <select class="form-control mb-2" id="parent_category">
                                    <option value="">-- Chọn danh mục cha --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" id="confirm_add_category" class="btn btn-primary">Thêm danh
                                    mục</button>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Danh mục sản phẩm</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-secondary mb-3" onclick="addVariant()">Add
                                Variant</button>
                            <div id="additional-variants"></div>
                            <div id="variants">
                                <!-- Default variant -->
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
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-danger"
                                            onclick="removeVariant(this)">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




                <div class="card shadow mb-4">
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
                </div>


            </div>





            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success mr-2">Lưu sản phẩm</button>
                <a href="{{ route('admin.products.listProduct') }}" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <x-admin.data-table-styles />
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar {
            border-radius: 5px 5px 0 0;
            border: 1px solid #ddd;
        }

        .ql-container {
            border-radius: 0 0 5px 5px;
            border: 1px solid #ddd;
        }

        .image-preview {
            position: relative;
            display: inline-block;
        }

        .remove-image {
            position: absolute;
            top: 5px;
            /* Đặt dấu X gần trên cùng */
            right: 5px;
            /* Đặt dấu X gần bên phải */
            background-color: rgba(0, 0, 0, 0.5);
            /* Màu nền bán trong suốt */
            color: white;
            font-size: 18px;
            /* Kích thước chữ nhỏ hơn một chút */
            padding: 3px 6px;
            /* Điều chỉnh khoảng cách bên trong */
            border-radius: 50%;
            /* Làm cho dấu X tròn */
            cursor: pointer;
            z-index: 10;
            /* Đảm bảo dấu X hiển thị trên ảnh */
        }

        .remove-image:hover {
            background-color: rgba(255, 0, 0, 0.7);
            /* Màu nền thay đổi khi hover */
        }
    </style>
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        var quill = new Quill('#snow-editor', {
            theme: 'snow',
            placeholder: 'Nhập nội dung sản phẩm tại đây...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'header': [1, 2, 3, false]
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['link', 'image', 'video'],
                    [{
                        'align': []
                    }],
                    ['clean']
                ]
            }
        });

        // Đảm bảo rằng khi người dùng submit form, nội dung từ Quill được gửi đi
        document.querySelector('form').addEventListener('submit', function() {
            // Lấy nội dung từ Quill Editor
            var description = quill.root.innerHTML;

            // Tạo một input ẩn để chứa nội dung mô tả
            var descriptionInput = document.createElement('input');
            descriptionInput.setAttribute('type', 'hidden');
            descriptionInput.setAttribute('name', 'description');
            descriptionInput.setAttribute('value', description);

            // Thêm input này vào trong form
            this.appendChild(descriptionInput);
        });


        document.getElementById('confirm_add_category').addEventListener('click', function() {
            const name = document.getElementById('new_category_name').value;
            const parentCategory = document.getElementById('parent_category').value;

            if (!name) {
                alert('Vui lòng nhập tên danh mục!');
                return;
            }

            fetch('{{ route('admin.categories.addAjax') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        name: name,
                        id_parent: parentCategory,
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert(data.message);

                        // Thêm danh mục mới vào danh sách checkbox
                        const categoryList = document.querySelector('.form-group .border');
                        const newCategory = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="category_${data.category.id}" name="categories[]" value="${data.category.id}">
                        <label class="form-check-label" for="category_${data.category.id}">${data.category.name}</label>
                    </div>
                `;
                        categoryList.insertAdjacentHTML('beforeend', newCategory);

                        // Reset input fields
                        document.getElementById('new_category_name').value = '';
                        document.getElementById('parent_category').value = '';
                    } else {
                        alert(data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('Đã xảy ra lỗi. Vui lòng thử lại.');
                });
        });








        function addVariant() {
            const variantsContainer = document.getElementById('additional-variants');
            const maxVariants = 5; // Giới hạn số lượng variants

            const currentVariantCount = variantsContainer.children.length;

            if (currentVariantCount < maxVariants) {
                // HTML để thêm một variant mới với sku và status
                const newVariant = `
    <div class="variant-row mb-3 d-flex align-items-center">
        <input class="form-control me-2" type="text" name="sku[]" placeholder="Enter SKU" required>
        <select class="form-control me-2" name="status[]">
            <option value="active" selected>Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <button type="button" class="btn btn-danger" onclick="removeVariant(this)">Remove</button>
    </div>
    `;

                // Thêm HTML vào phần tử container
                variantsContainer.insertAdjacentHTML('beforeend', newVariant);
            } else {
                alert('Tối đa chỉ thêm 5 variants.');
            }
        }

        // Hàm để xóa variant
        function removeVariant(button) {
            button.closest('.variant-row').remove();
        }



        function addImage() {
            const imagesContainer = document.getElementById('additional-images');
            const maxImages = 4; // Số lượng ảnh tối đa
            const currentImageCount = imagesContainer.children.length;

            if (currentImageCount < maxImages) {
                const imageTemplate = `
            <div class="mb-3 d-flex align-items-center position-relative">
                <input class="form-control me-2" type="file" name="additional_images[]" onchange="previewImage(event)">
                <div class="image-preview mt-2" style="display: none;">
                    <img class="img-thumbnail" style="max-width: 200px;" />
                    <span class="remove-image" onclick="removeImage(this)">×</span>
                </div>
            </div>
        `;
                imagesContainer.insertAdjacentHTML('beforeend', imageTemplate);
            } else {
                alert('Tối Đa Chỉ Thêm Được 4 Hình Ảnh.');
            }
        }

        function previewImage(event) {
            const input = event.target;
            const previewContainer = input.closest('.mb-3').querySelector('.image-preview');
            const previewImage = previewContainer.querySelector('img');

            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        // Hàm xóa hình ảnh khi nhấn vào dấu "×"
        function removeImage(button) {
            const imageContainer = button.closest('.mb-3'); // Tìm phần tử cha chứa ảnh và input
            imageContainer.remove(); // Xóa phần tử cha đó
        }
    </script>
@endpush --}}
