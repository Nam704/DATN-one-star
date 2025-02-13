@extends('admin.layouts.layout')
@section('content')
<<<<<<< HEAD
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 mt-2">Thêm mới sản phẩm</h1>
        </div>

        <div class="container">
            <form action="{{ route('admin.products.addPostProduct') }}" method="post" enctype="multipart/form-data"
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
                                    <textarea id="description" name="description" class="form-control" placeholder="Nhập mô tả sản phẩm...">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 p-2">
                                <label class="fw-bold mb-0">Dữ liệu sản phẩm —</label>
                                <select class="form-select w-auto" id="productType">
                                    <option value="simple">Sản phẩm đơn giản</option>
                                    <option value="variable">Sản phẩm có biến thể</option>
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
                                            <a href="#" class="list-group-item list-group-item-action"
                                                id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview">
                                                <i class="bi bi-house-door me-2"></i>Tổng quan
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action"
                                                id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory">
                                                <i class="bi bi-box me-2"></i>Kiểm kê kho hàng
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action"
                                                id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping">
                                                <i class="bi bi-truck me-2"></i>Giao hàng
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action"
                                                id="linked-products-tab" data-bs-toggle="tab"
                                                data-bs-target="#linked-products">
                                                <i class="bi bi-link-45deg me-2"></i>Sản phẩm được liên kết
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action"
                                                id="attributes-tab" data-bs-toggle="tab" data-bs-target="#attributes">
                                                <i class="bi bi-tags me-2"></i>Các thuộc tính
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action"
                                                id="variations-tab" data-bs-toggle="tab" data-bs-target="#variations">
                                                <i class="bi bi-sliders me-2"></i>Các biến thể
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action"
                                                id="advanced-tab" data-bs-toggle="tab" data-bs-target="#advanced">
                                                <i class="bi bi-gear me-2"></i>Nâng cao
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Main Content -->
                                    <div class="col-md-9 col-lg-10">
                                        <div class="tab-content p-3" id="productTabsContent">
                                            <!-- Tổng quan Tab -->
                                            <div class="tab-pane fade" id="overview" role="tabpanel">
                                                <div id="simpleProductFields">
                                                    <div class="mb-3">
                                                        <label class="form-label">Giá bán thường
                                                            (đ)</label>
                                                        <input type="number" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Giá khuyến mãi
                                                            (đ)</label>
                                                        <input type="number" class="form-control">
                                                    </div>
                                                    <a href="#" class="text-primary">Lên lịch</a>
                                                </div>
                                            </div>

                                            <!-- Kiểm kê kho hàng Tab -->
                                            <div class="tab-pane fade" id="inventory" role="tabpanel">
                                                <p>Content for Kiểm kê kho hàng...</p>
                                            </div>

                                            <!-- Giao hàng Tab -->
                                            <div class="tab-pane fade" id="shipping" role="tabpanel">
                                                <p>Content for Giao hàng...</p>
                                            </div>

                                            <!-- Sản phẩm được liên kết Tab -->
                                            <div class="tab-pane fade" id="linked-products" role="tabpanel">
                                                <p>Content for Sản phẩm được liên kết...</p>
                                            </div>

                                            <!-- Các thuộc tính Tab -->
                                            <div class="tab-pane fade" id="attributes" role="tabpanel">

                                                <div class="container-fluid">
                                                    <!-- Header Buttons -->
                                                    <div class="mb-2">
                                                        <label for="attributeSelect" class="form-label">Chọn Thuộc
                                                            Tính(nhiều)</label>
                                                        <select id="attributeSelect" class="form-select">

                                                        </select>
                                                    </div>

                                                    <div id="container-accords" class="accordion"></div>
                                                </div>


                                            </div>

                                            <!-- Các biến thể Tab -->
                                            <div class="tab-pane fade" id="variations" role="tabpanel">
                                                <p>Content for Các biến thể...</p>
                                            </div>

                                            <!-- Nâng cao Tab -->
                                            <div class="tab-pane fade" id="advanced" role="tabpanel">
                                                <p>Content for Nâng cao...</p>
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
                                <h6 class="m-0 font-weight-bold text-primary">Danh mục sản phẩm</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Chọn danh mục:</label>
                                    <div class="border p-2 rounded">
                                        @foreach ($categories as $category)
                                            @if ($category->id_parent == null)
                                                <div class="category-item" id="category_{{ $category->id }}">
                                                    <label class="toggle-subcategories"
                                                        data-category-id="{{ $category->id }}">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="id_category" value="{{ $category->id }}">
                                                        {{ $category->name }}
                                                    </label>

                                                    <div class="subcategory-container"
                                                        id="subcategories_{{ $category->id }}" style="display: none;">
                                                        @foreach ($categories as $child)
                                                            @if ($child->id_parent == $category->id)
                                                                <div class="category-item"
                                                                    id="category_{{ $child->id }}">
                                                                    <label class="toggle-subcategories"
                                                                        data-category-id="{{ $child->id }}">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="id_category"
                                                                            value="{{ $child->id }}">
                                                                        {{ $child->name }}
                                                                    </label>

                                                                    <!-- Danh mục cháu -->
                                                                    <div class="subcategory-container"
                                                                        id="subcategories_{{ $child->id }}"
                                                                        style="display: none;">
                                                                        @foreach ($categories as $grandchild)
                                                                            @if ($grandchild->id_parent == $child->id)
                                                                                <div class="category-item">
                                                                                    <input class="form-check-input"
                                                                                        type="checkbox" name="id_category"
                                                                                        value="{{ $grandchild->id }}">
                                                                                    <label class="form-check-label">
                                                                                        {{ $grandchild->name }}
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
                                            @endif
                                        @endforeach
                                    </div>
                                    @error('id_category')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label for="new_category_name" class="font-weight-bold">Thêm danh mục mới:</label>
                                    <div id="add-category-section">
                                        <input type="text" class="form-control mb-2" id="new_category_name"
                                            name="new_category_name" placeholder="Tên danh mục mới"
                                            style="display: none;">
                                        <select class="form-control mb-2" id="id_parent" name="id_parent"
                                            style="display: none;">
                                            <option value="">-- Chọn danh mục cha --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" id="confirm_add_category" class="btn btn-primary mb-2"
                                            style="display: none;">Xác nhận thêm</button>
                                    </div>
                                    <button type="button" id="show_add_category" class="btn btn-link">+ Add new
                                        category</button>
                                </div>
                            </div>
                        </div>

                        <!-- Product Image -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Ảnh sản phẩm</h6>
                            </div>
                            <div class="card-body">
                                <input type="file" id="productImage" class="form-control mb-3" accept="image/*">
                                <div id="imagePreview" class="text-center"></div>
                            </div>
                        </div>

                        <!-- Product Album -->
                        <div class="card shadow mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Album sản phẩm</h6>
                                <button type="button" id="addAlbumImage" class="btn btn-primary btn-sm">+</button>
                            </div>
                            <div class="card-body">
                                <input type="file" id="albumImages" class="form-control mb-3" accept="image/*"
                                    multiple style="display: none;">
                                <div id="albumPreview" class="row g-2"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success mr-2">Thêm sản phẩm</button>
                    <a href="{{ route('admin.products.listProduct') }}" class="btn btn-secondary ms-2">Quay lại</a>
=======
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 mt-2   ">Thêm mới sản phẩm</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{route('admin.products.addPostProduct')}}" method="post" enctype="multipart/form-data" class="form">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror " id="name" name="name" placeholder="Nhập tên sản phẩm..." value="{{old('name')}}">
                    @error('name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="id_brand">Brand</label>
                    <select class="form-control @error('id_brand') is-invalid @enderror" id="id_brand" name="id_brand">
                        <option selected>--Chọn thương hiệu---</option>
                        @foreach($brands as $brand)
                        <option value="{{$brand->id}}" {{ old('id_brand') == $brand->id ?'selected': '' }}> {{$brand->name}} </option>
                        @endforeach
                    </select>
                    @error('id_brands')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="id_category">Danh mục</label>
                    <select class="form-control @error('id_category') is-invalid @enderror" id="id_category" name="id_category">
                        <option selected>--Chọn danh mục---</option>
                        @foreach($categories as $category)
                        <option value="{{$category->id}}" {{ old('id_category') == $category->id ?'selected': '' }}> {{$category->name}} </option>
                        @endforeach
                    </select>
                    @error('id_category')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" rows="5" id="description" name="description" placeholder="Nhập mô tả..."></textarea>
                    @error('description')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="hinh_anh" class="form-label ">Tải ảnh lên:</label>
                    <input type="file" name="image_primary" id="image_primary" class="form-control-file border @error('image_primary') is-invalid @enderror">
                    @error('image_primary')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="status">Status</label>
                    <select class="form-select" aria-label="Default select example" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <button type="submit" name="submit" class="btn btn-success">Xác nhận</button>
                    <a href="{{ route('admin.products.listProduct')}}"><button type="button" class="btn btn-success">Quay lại</button></a>
>>>>>>> 7e4fffbd8e3ac0d6e2a89b68e60e3b1247343418
                </div>
            </form>
        </div>
    </div>
<<<<<<< HEAD
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

    {{-- xử lí ảnh --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productImage = document.getElementById('productImage');
            const imagePreview = document.getElementById('imagePreview');
            const albumImages = document.getElementById('albumImages');
            const albumPreview = document.getElementById('albumPreview');
            const addAlbumImage = document.getElementById('addAlbumImage');

            // Product Image Preview
            productImage.addEventListener('change', function(e) {

                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    const imageUrl = URL.createObjectURL(file);
                    imagePreview.innerHTML = `
                            <img src="${imageUrl}" alt="Product Preview" class="img-fluid mb-2" style="max-height: 200px;">
                        `;
                }
            });

            // Album Images Preview
            function handleAlbumImages(e) {
                event.preventDefault();
                const files = e.target.files;
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const imageUrl = URL.createObjectURL(file);
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-3';
                    col.innerHTML = `
                            <div class="position-relative">
                                <img src="${imageUrl}" alt="Album Image Preview" class="img-fluid rounded">
                                <button style="width: 10px" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1">&times;</button>
                            </div>
                        `;
                    albumPreview.appendChild(col);

                    // Delete button functionality
                    col.querySelector('.btn-danger').addEventListener('click', function() {
                        albumPreview.removeChild(col);
                    });
                }
            }

            albumImages.addEventListener('change', handleAlbumImages);

            // Add Album Image button
            addAlbumImage.addEventListener('click', function() {
                albumImages.click();
            });
        });
    </script>
    <!-- <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.0/tinymce.min.js"></script>
    {{-- tiny --}}
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



    {{-- dữ liệu sản phẩm --}}
    <script>
        const productTypeSelect = document.getElementById('productType');
        const allTabs = document.querySelectorAll('.list-group-item');
        const allTabPanels = document.querySelectorAll('.tab-pane');
        const tabLinks = document.querySelectorAll('.list-group-item');

        function toggleTabs() {
            if (productTypeSelect.value === 'simple') {


                // Ẩn các tab và nội dung không liên quan đến sản phẩm đơn giản
                document.getElementById('attributes-tab').style.display = 'none';
                document.getElementById('variations-tab').style.display = 'none';
                document.getElementById('attributes').style.display = 'none';
                document.getElementById('variations').style.display = 'none';

                // Hiển thị các tab liên quan đến sản phẩm đơn giản
                document.getElementById('overview-tab').style.display = 'block';
                document.getElementById('inventory-tab').style.display = 'block';
                document.getElementById('shipping-tab').style.display = 'block';
                document.getElementById('linked-products-tab').style.display = 'block';
                document.getElementById('advanced-tab').style.display = 'block';

                // Chọn tab đầu tiên của mỗi mục khi là sản phẩm đơn giản
                selectTab('overview-tab');
            } else {
                // Hiển thị tất cả các tab khi chọn sản phẩm biến thể
                document.getElementById('attributes-tab').style.display = 'block';
                document.getElementById('variations-tab').style.display = 'block';
                document.getElementById('attributes').style.display = 'block';
                document.getElementById('variations').style.display = 'block';

                // Hiển thị các tab liên quan đến sản phẩm biến thể
                document.getElementById('overview-tab').style.display = 'none';
                document.getElementById('inventory-tab').style.display = 'block';
                document.getElementById('shipping-tab').style.display = 'block';
                document.getElementById('linked-products-tab').style.display = 'block';
                document.getElementById('advanced-tab').style.display = 'block';

                // Chọn tab đầu tiên khi là sản phẩm biến thể
                selectTab('inventory-tab');
            }
        }

        function selectTab(tabId) {
            tabLinks.forEach(tab => tab.classList.remove('active')); // Remove 'active' class from all tabs
            document.getElementById(tabId).classList.add('active'); // Add 'active' class to the selected tab

            // Hiển thị nội dung của tab đã chọn
            allTabPanels.forEach(panel => panel.style.display = 'none'); // Ẩn tất cả nội dung các tab
            const tabContentId = tabId.replace('-tab', ''); // Loại bỏ '-tab' để lấy id nội dung
            document.getElementById(tabContentId).style.display = 'block'; // Hiển thị nội dung tab đã chọn
        }

        // Ban đầu kiểm tra loại sản phẩm
        toggleTabs();

        // Lắng nghe sự kiện thay đổi trên select
        productTypeSelect.addEventListener('change', function() {
            console.log('check');
            toggleTabs();
        });

        // Xử lý sự kiện click vào tab
        tabLinks.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = tab.id;
                selectTab(tabId); // Chọn tab khi click vào
            });
        });





        // Thuộc tính

        function updateDisabledOptions() {
            const select = document.getElementById('value-select');
            const selectedTags = Array.from(document.getElementById('selected-tags').children);
            const selectedValues = selectedTags.map(tag => tag.innerText.replace(/\s×$/, ''));

            Array.from(select.options).forEach(option => {
                option.disabled = selectedValues.includes(option.value);
            });
        }



        // attributeSelect
        // Object lưu trữ các thuộc tính đã chọn
        const selectedAttributes = {};

        // Hàm tạo accordion
        const renderAccord = (id, title) => {
            if (selectedAttributes[id]) {
                // Nếu thuộc tính đã tồn tại, không tạo thêm
                alert(`Thuộc tính "${title}" đã được thêm!`);
                return;
            }

            // Đánh dấu thuộc tính đã được thêm
            selectedAttributes[id] = true;

            const newAccord = `
                <div id="container-accord-${id}" class="accordion-item ">
                    <h2 class="accordion-header d-flex align-items-center justify-content-between px-2" id="heading-${id}">
                        <button class="attribute-button accordion-button"
                            type="button"
                            id="button-${id}"
                            data-idAttribute="${id}"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-${id}"
                            aria-expanded="true"
                            aria-controls="collapse-${id}">
                            ${title}
                        </button>
                         <button type="button" class="btn btn-sm btn-danger ms-2 delete-attribute-buttons"
                            data-idAttribute="${id}"
                            aria-label="Xóa">
                            &times;
                        </button>
                    </h2>
                    <div id="collapse-${id}"
                        class="accordion-collapse collapse"
                        aria-labelledby="heading-${id}"
                        data-bs-parent="#container-accords">
                        <div class="accordion-body">
                            <!-- Chọn giá trị cho ${title} -->

                            <h6>Chọn giá trị (nhiều) - click vào giá trị đã thêm để xóa:</h6>
                            <select class="attribute-value-select form-select" id="attribute-value-${id}">
                            </select>
                             <div id="selected-tags-${id}" class="d-flex flex-wrap gap-2 mb-2 mt-2  ">
                                <!-- Các tag được chọn sẽ hiển thị ở đây -->
                            </div>
                        </div>
                    </div>
                </div>
    `;

            // Thêm accordion vào container
            document.getElementById("container-accords").insertAdjacentHTML("beforeend", newAccord);
        };

        // bắt sự kiện thêm thuộc tính
        document.getElementById("attributeSelect").addEventListener("change", function(e) {
            const attributeId = e.target.value;
            const selectedOption = this.options[this.selectedIndex]; // Lấy option được chọn
            const text = selectedOption.textContent || selectedOption.innerText;

            if (attributeId) {
                renderAccord(attributeId, text);
                document.getElementById(`button-${attributeId}`).addEventListener('click', function(e) {


                    if (document.getElementById(`attribute-value-${attributeId}`).innerHTML.trim() === '') {
                        fetch(`http://127.0.0.1:8000/api/attribute-values/${attributeId}`, {
                                method: 'GET',
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log(data);

                                const values = data.data;
                                console.log(values);

                                let optionHtml = '<option value="">Chọn:</option>';
                                values.forEach(value => {
                                    optionHtml +=
                                        `<option value="${value.id}">${value.value}</option>`;
                                });
                                document.getElementById(`attribute-value-${attributeId}`).innerHTML =
                                    optionHtml;

                            })
                            .catch(error => {
                                console.error('Lỗi khi gửi dữ liệu:', error);
                            });
                    }

                });

                document.getElementById(`attribute-value-${attributeId}`).addEventListener('change', function(e) {
                    const valueId = e.target.value;
                    const selectedOption = this.options[this.selectedIndex];
                    const text = selectedOption.textContent || selectedOption.innerText;

                    if (valueId) {
                        const tag = document.createElement('span');
                        tag.className = 'badge bg-primary p-2';
                        tag.id = 'tag-' + valueId;

                        tag.innerText = text;
                        tag.style.cursor = 'pointer';
                        tag.addEventListener('click', function() {
                            this.remove();
                            updateDisabledOptions();
                        });

                        if(document.getElementById(`tag-${valueId}`)){
                            alert('Giá trị đã được chọn');
                            return;
                        }
                        document.getElementById(`selected-tags-${attributeId}`).appendChild(tag);
                        updateDisabledOptions();
                    }
                });
                Array.from(document.getElementsByClassName('delete-attribute-buttons')).forEach(button => {
                    button.addEventListener('click', function(e) {
                        const attributeId = this.getAttribute('data-idAttribute');
                        document.getElementById(`container-accord-${attributeId}`).remove();
                        delete selectedAttributes[attributeId];
                    });
                });
            }
        });

    </script>

    {{-- call api attribute ban đầu --}}
    <script>
        fetch('http://127.0.0.1:8000/api/attributes', {
                method: 'GET',
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log(data);

                const attributes = data.data;
                console.log(attributes);

                let optionHtml = '<option value="">Chọn:</option>';
                attributes.forEach(attribute => {
                    optionHtml += `<option value="${attribute.id}">${attribute.name}</option>`;
                });
                document.getElementById('attributeSelect').innerHTML = optionHtml;

            })
            .catch(error => {
                console.error('Lỗi khi gửi dữ liệu:', error);
            });


        console.log(optionHtml);



        // document.addEventListener('DOMContentLoaded', function() {

        //     attributeSelect.addEventListener('change', function() {
        //         const attributeId = this.value;
        //         if (attributeId) {
        //             $.ajax({
        //                 type: 'GET',
        //                 url: `/attributes/${attributeId}`,
        //                 success: function(response) {
        //                     if (response.success) {
        //                         renderAccord(attributeId, response.data.name);
        //                     } else {
        //                         alert(response.message || 'Có lỗi xảy ra, vui lòng thử lại.');
        //                     }
        //                 },
        //                 error: function(xhr) {
        //                     alert('Lỗi khi gửi dữ liệu: ' + xhr.responseText);
        //                 }
        //             });
        //         }
        //     });
        // });
    </script>
@endpush
=======
</div>
<!-- /.container-fluid -->
@endsection
@push('styles')
<x-admin.data-table-styles />
@endpush

@push('scripts')
<x-admin.data-table-scripts />
@endpush
>>>>>>> 7e4fffbd8e3ac0d6e2a89b68e60e3b1247343418
