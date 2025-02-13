@extends('admin.layouts.layout')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 mt-2">Thêm mới sản phẩm</h1>
        </div>

        <div>
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
                                    <option value="variants">Sản phẩm có biến thể</option>
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
                                            <div class="tab-pane fade show" id="overview" role="tabpanel">
                                                <div id="simpleProductFields">
                                                    <div class="mb-3">
                                                        <label class="form-label">Giá bán thường
                                                            (đ)</label>
                                                        <input name="price" type="number" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Giá khuyến mãi
                                                            (đ)</label>
                                                        <input name="price_sale" type="number" class="form-control">
                                                    </div>

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

                                                <div>
                                                    <!-- Header Buttons -->
                                                    <div class="mb-2">
                                                        <label for="attributeSelect" class="form-label">Chọn Thuộc
                                                            Tính (nhiều)</label>
                                                        <select id="attributeSelect" class="form-select">

                                                        </select>
                                                    </div>

                                                    <div id="container-accords" class="accordion"></div>
                                                </div>

                                                <div class="d-flex justify-content-end mt-3">
                                                    <button type="button" id="save-attribute-btn"
                                                        class="btn btn-primary">
                                                        Lưu chọn thuộc tính
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Các biến thể Tab -->
                                            <div class="tab-pane fade" id="variations" role="tabpanel">

                                                <div class="button-container">
                                                    <button type="button" id="add-new-var"
                                                        class="btn btn-outline-primary mb-2">
                                                        Thêm mới biến thể
                                                    </button>
                                                    <button type="button" id="save-var" class="btn btn-primary mb-2">
                                                        Lưu Biến thể
                                                    </button>
                                                </div>

                                                <div id="container-variations"></div>
                                                <div id="data-vars"></div>

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
                                <h4 class="m-0 font-weight-bold text-primary">Danh mục sản phẩm</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Chọn danh mục:</label>
                                    <div class="border p-2 rounded" id="categories-container">
                                        {{-- danh mục đc ren ở đây nè --}}
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
                                <h4 class="m-0 font-weight-bold text-primary">Ảnh sản phẩm</h4>
                            </div>
                            <div class="card-body">
                                <input name="image-primary" type="file" id="productImage" class="form-control mb-3"
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

                <input type="text" id="product-type" name="product_type" value="simple" style="display: none;">

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
                        alert('Thêm danh mục thành công!');
                        renderRootCategories();
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
        let isFisrtRenderVariants = false;

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

        toggleTabs();

        productTypeSelect.addEventListener('change', function() {
            toggleTabs();
            document.getElementById('product-type').value = productTypeSelect.value;
        });


        tabLinks.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = tab.id;
                selectTab(tabId);
                if (tabId === 'variations-tab' && isFisrtRenderVariants === false) {
                    renderProductVariations();
                    isFisrtRenderVariants = true;
                    indexVariant++;
                }
            });
        });

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

        let indexVariant = 1;

        let attributeState = [];

        let attributeValues = [];

        document.getElementById('add-new-var').addEventListener('click', function() {
            renderProductVariations();
            indexVariant++;
        });

        document.getElementById('save-var').addEventListener('click', function(e) {

            const dataContainer = document.getElementById('data-vars');

            const variantsTagContainer = document.getElementsByClassName('container-accords');
            Array.from(variantsTagContainer).forEach((accord) => {

                const inputData = document.createElement('input');
                inputData.setAttribute('type', 'hidden');
                inputData.setAttribute('name', 'variants[]');

                inputData.setAttribute('value', JSON.stringify(
                    Array.from(accord.getElementsByTagName('select')).map(
                        tag => ({
                            attribute_id: tag.getAttribute('data-idAttribute'),
                            attribute_value_id: tag.value
                        })
                    )
                ));

                const inputTotal = document.createElement('input');

                inputTotal.setAttribute('type', 'hidden');
                inputTotal.setAttribute('name', 'total_variant');
                inputTotal.setAttribute('value', variantsTagContainer.length);

                dataContainer.appendChild(inputTotal);
                dataContainer.appendChild(inputData);

                e.target.disabled = true;
                alert('Đã lưu');

            });
        });
        // Hàm tạo accordion
        const renderAccord = (id, title) => {
            if (selectedAttributes[id]) {
                // Nếu thuộc tính đã tồn tại, không tạo thêm
                alert(`Thuộc tính "${title}" đã được thêm!`);
                return;
            }

            // Đánh dấu thuộc tính đã được thêm
            selectedAttributes[id] = true;

            const newAccord = /*html*/ `
                <div data-nameAttribute="${title}" data-idAttribute="${id}" style="background-color: white" id="container-accord-${id}" class="accordion-item tag-attribute">
                    <h2 class="accordion-header d-flex align-items-center justify-content-between px-2" id="heading-${id}">
                        <button style="background-color: white" class="attribute-button accordion-button" 
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
                             <div id="selected-tags-${id}" class="attribute-value d-flex flex-wrap gap-2 mb-2 mt-2">
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
            const selectedOption = this.options[this.selectedIndex];
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
                        tag.className = 'tag-attribute-value badge bg-primary p-2';
                        tag.id = 'tag-' + valueId;
                        tag.setAttribute('data-idAttributeValue', valueId);
                        tag.setAttribute('data-nameAttributeValue', text);

                        tag.innerText = text;
                        tag.style.cursor = 'pointer';
                        tag.addEventListener('click', function() {
                            this.remove();
                            updateDisabledOptions();
                        });

                        if (document.getElementById(`tag-${valueId}`)) {
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

        document.querySelector('#save-attribute-btn').addEventListener('click', function() {


            attributeState = Array.from(document.getElementsByClassName('tag-attribute')).map((accord, i) => {
                console.log(accord);

                return {
                    attribute_id: accord.getAttribute('data-idAttribute'),
                    attribute_name: accord.getAttribute('data-nameAttribute'),
                    values: Array.from(accord.querySelectorAll('.tag-attribute-value')).map(tag => ({
                        id: tag.getAttribute('data-idAttributeValue'),
                        value: tag.getAttribute('data-nameAttributeValue')
                    }))
                };
            })
            alert('Đã lưu thuộc tính. Chuyển sang tab các biến thể để cấu hình.');
            console.log(attributeState);
        });

        function renderProductVariations() {
            const renderVariant = (index) => {
                const renderOptionValue = (values) => {
                    return values.map((value) => /*html*/ `
                        <option value="${value.id}">${value.value}</option>
                    `).join('');
                };

                return attributeState.map((attribute, i) => /*html*/ `
                    <div class="d-flex align-items-center pr-2">
                        
                        <div>${attribute.attribute_name}:</div>
                        <select data-nameAttribute="${attribute.attribute_name}" data-idAttribute="${attribute.attribute_id}" class="form-select select-attribute-value-${index}" id="basic-select-${i}" style="margin-right: 15px">
                            <option selected>Chọn...</option>
                            ${renderOptionValue(attribute.values)}
                        </select>
                    </div>
                `).join('');
            };

            const htmlVariations = /*html*/ `
                <div id="variant-${indexVariant}" class="mb-3 container-accords">
                    <div class="accordion" id="container-accords">
                        <div class="accordion-item">
                            <!-- Phần bên ngoài, chứa <select> -->
                            <div class="accordion-header d-flex align-items-center justify-content-between"
                                id="heading-${indexVariant}">
                                <div class="d-flex p-2">${renderVariant(indexVariant)}</div>
                                <!-- Mũi tên mở accordion -->
                                <div style="margin-right: 15px">
                                    <button class="btn btn-sm ms-2 btn-primary" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-${indexVariant}" aria-expanded="false"
                                        aria-controls="collapse-${indexVariant}">
                                        ▼
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger ms-2 delete-variations-buttons"
                                        data-deleteVariantId="${indexVariant}" aria-label="Xóa">
                                        &times;
                                    </button>
                                </div>
                            </div>
                            <!-- Nội dung accordion -->
                            <div id="collapse-${indexVariant}" class="accordion-collapse collapse"
                                aria-labelledby="heading-${indexVariant}" data-bs-parent="#container-accords">
                                <div class="accordion-body data-form-variant">
                                    <!-- Nội dung bổ sung trong accordion -->
                                    <div class="mb-3">
                                        <label for="image-product-variant" class="form-label">Ảnh</label>
                                        <input  name="image_product_variant[]" type="file" class="form-control" id="image-product-variant" accept="image/*">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="price-product-variant" class="form-label">Giá</label>
                                        <input name="price_product_variant[]" type="number" class="form-control" id="price-product-variant" min="0" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="quantity-product-variant" class="form-label">Số lượng</label>
                                        <input name="quantity_product_variant[]" type="number" class="form-control" id="quantity-product-variant" min="0" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description-product-variant" class="form-label">Mô tả</label>
                                        <textarea  name="description_product_variant[]" class="form-control" id="description-product-variant" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('container-variations').insertAdjacentHTML('beforeend', htmlVariations);

            Array.from(document.getElementsByClassName(`select-attribute-value-${indexVariant}`)).forEach(select => {
                select.addEventListener('change', function(e) {
                    console.log(e.target.value);
                });
            });

            document.getElementById(`variant-${indexVariant}`).addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-variations-buttons')) {
                    const deleteVariantId = e.target.getAttribute('data-deleteVariantId');

                    document.getElementById(`variant-${deleteVariantId}`).remove();

                }
            });

        }
    </script>


    {{-- call api categories  --}}
    <script>
        function renderCategories(categories, containerId) {

            let arrIds = [];
            const html = categories.map(category => {
                arrIds.push(category.id);
                return /*html*/ `
                <div class="category-item" id="category_${category.id}">
                    <label class="toggle-subcategories" data-category-id="${category.id}">
                        <input type="checkbox" id="checkbox-category-id-${category.id}" name="categories[]" class="form-check-input" name="id_category" value="${category.id}">
                        ${category.name}
                    </label>
                    <div class="subcategory-container" id="subcategories_${category.id}"></div>
                </div>`
            }).join('');

            document.getElementById(containerId).innerHTML = html;

            arrIds.forEach(id => {
                document.getElementById(`checkbox-category-id-${id}`).addEventListener('change', function() {
                    if (this.checked) {
                        fetchAndRenderSubcategories(id);
                    }
                });
            });
        }

        function fetchAndRenderSubcategories(categoryId) {
            const subcategoryContainer = document.getElementById(`subcategories_${categoryId}`);
            fetch(`http://127.0.0.1:8000/api/categories/getChildCategories/${categoryId}`, {
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
                    renderCategories(data.data, `subcategories_${categoryId}`);
                })
                .catch(error => {
                    console.error('Error fetching subcategories:', error);
                });
        }

        function renderRootCategories() {
            fetch('http://127.0.0.1:8000/api/categories/getRootCategories', {
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
                    renderCategories(data.data, 'categories-container');
                })
                .catch(error => {
                    console.error('Error fetching root categories:', error);
                });
        }
        renderRootCategories();
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


        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.form-select').forEach((select) => {
                select.addEventListener('click', (event) => {
                    event.stopPropagation();
                });
            });
        });
    </script>
@endpush
