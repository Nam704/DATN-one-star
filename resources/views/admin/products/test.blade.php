@extends('admin.layouts.layout')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 mt-2">Thêm mới sản phẩm</h1>
        </div>

        <div class="container">
            <form action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data" class="form">
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
                                                <div class="card shadow mb-4">
                                                    <div class="card-header">
                                                        <h6 class="m-0 font-weight-bold text-primary">Thuộc tính sản phẩm
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Chọn thuộc tính:</label>
                                                            <select class="form-select mb-3" id="attributeSelect">
                                                                <option value="">-- Chọn thuộc tính --</option>
                                                                @foreach ($attributes as $attribute)
                                                                    <option value="{{ $attribute->id }}"
                                                                        data-name="{{ $attribute->name }}">
                                                                        {{ $attribute->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                            <div id="selectedAttributes" class="border rounded p-3">
                                                                @foreach ($attributes as $attribute)
                                                                    <div class="attribute-item mb-3"
                                                                        id="attribute_{{ $attribute->id }}"
                                                                        style="display: none;"
                                                                        data-attribute-id="{{ $attribute->id }}">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                            <h6 class="mb-0">{{ $attribute->name }}</h6>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-danger remove-attribute"
                                                                                data-attribute-id="{{ $attribute->id }}">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                        <div class="attribute-values ms-3">
                                                                            @foreach ($attribute->attributeValues as $value)
                                                                                <div class="form-check">
                                                                                    <input type="checkbox"
                                                                                        class="form-check-input attribute-value-checkbox"
                                                                                        name="attribute_values[{{ $attribute->id }}][]"
                                                                                        value="{{ $value->id }}"
                                                                                        data-value-name="{{ $value->value }}"
                                                                                        id="value_{{ $value->id }}">
                                                                                    <label class="form-check-label"
                                                                                        for="value_{{ $value->id }}">
                                                                                        {{ $value->value }}
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="button" id="saveAttributes"
                                                                class="btn btn-primary mt-3">Lưu thuộc tính</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>





                                            <!-- Các biến thể Tab -->
                                            <div class="tab-pane fade" id="variations" role="tabpanel">
                                                <div class="card shadow mb-4">
                                                    <div class="card-header">
                                                        <h6 class="m-0 font-weight-bold text-primary">Các biến thể sản phẩm
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex gap-2 mb-3">
                                                            <button type="button" id="createVariations"
                                                                class="btn btn-primary">
                                                                <i class="fas fa-plus"></i> Tạo biến thể
                                                            </button>
                                                            <button type="button" id="addManualVariation"
                                                                class="btn btn-secondary">
                                                                <i class="fas fa-edit"></i> Thêm thủ công
                                                            </button>
                                                            <button type="button" id="saveVariations" class="btn btn-success">
                                                                <i class="fas fa-save"></i> Lưu thay đổi
                                                            </button>
                                                        </div>
                                                        <div id="variationsContainer" class="border rounded p-3">
                                                            <!-- Đây là nơi các select sẽ được render động -->
                                                        </div>
                                                    </div>
                                                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.0/tinymce.min.js"></script>
    <script src="{{ asset('admin/api/products.js') }}"></script>


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
    {{-- tiny --}}
    <script>
        let tempStorage = {
            attributes: [],
            selectedValues: {},
            variations: []
        };

        $('#saveAttributes').click(function() {
            tempStorage.attributes = [];
            tempStorage.selectedValues = {};

            $('.attribute-item:visible').each(function() {
                const $item = $(this);
                const attributeId = $item.data('attribute-id');
                const attributeName = $item.find('h6').text().trim();
                const selectedValues = [];

                $item.find('.attribute-value-checkbox:checked').each(function() {
                    selectedValues.push({
                        id: $(this).val(),
                        value: $(this).data('value-name')
                    });
                });

                if (selectedValues.length > 0) {
                    tempStorage.attributes.push({
                        id: attributeId,
                        name: attributeName
                    });
                    tempStorage.selectedValues[attributeId] = selectedValues;
                }
            });

            console.log('Saved to tempStorage:', tempStorage);

            if (tempStorage.attributes.length > 0) {
                Swal.fire('Thành công', 'Đã lưu thuộc tính vào bộ nhớ tạm', 'success');
            } else {
                Swal.fire('Lỗi', 'Vui lòng chọn ít nhất một thuộc tính và giá trị', 'error');
            }
        });

        // Generate variations
        $('#createVariations').click(function() {
            console.log('Current tempStorage:', tempStorage); // Debug log

            if (!tempStorage.attributes.length) {
                Swal.fire('Lỗi', 'Vui lòng lưu thuộc tính trước', 'error');
                return;
            }

            Swal.fire({
                title: 'Tạo biến thể',
                text: 'Bạn có muốn tạo tất cả các biến thể không?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Tạo',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    generateVariationSelects();
                }
            });
        });

        function generateVariations() {
            const combinations = generateCombinations(savedAttributes);
            const tbody = $('#variationsList');
            tbody.empty();

            combinations.forEach((combination, index) => {
                const row = $('<tr>');
                row.html(`
            <td><input type="checkbox" class="variation-select" name="selected_variations[]" value="${index}"></td>
            <td>${formatAttributes(combination)}</td>
            <td><input type="number" class="form-control" name="variations[${index}][price]"></td>
            <td><input type="number" class="form-control" name="variations[${index}][stock]"></td>
            <td><input type="text" class="form-control" name="variations[${index}][sku]"></td>
            <td>
                <button type="button" class="btn btn-sm btn-danger delete-variation">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `);
                tbody.append(row);
            });
        }


        function createVariationCard(index, isManual = false, combination = null) {
            const variationCard = $('<div>').addClass('card mb-3');
    
    // Card Header
    const header = $('<div>').addClass('card-header d-flex justify-content-between align-items-center');
    const title = $('<h6>').addClass('mb-0').text(`Biến thể ${index + 1}`);
    const headerButtons = $('<div>').addClass('d-flex gap-2');
    
    // Toggle button
    const toggleButton = $('<button>').addClass('btn btn-sm btn-primary collapse-toggle')
        .attr('type', 'button')
        .attr('data-bs-toggle', 'collapse')
        .attr('data-bs-target', `#variation${index}`)
        .html('<i class="fas fa-chevron-down"></i>');
        
    // Delete button
    const deleteButton = $('<button>').addClass('btn btn-sm btn-danger delete-variation')
        .attr('type', 'button')
        .html('<i class="fas fa-trash"></i>')
        .on('click', function(e) {
            e.preventDefault();
            variationCard.remove();
        });

    headerButtons.append(toggleButton, deleteButton);
    header.append(title, headerButtons);

    // Collapsible Body
    const body = $('<div>').addClass('collapse')
        .attr('id', `variation${index}`);
    const cardBody = $('<div>').addClass('card-body');
    
    // Attribute Selects Row
    const selectsRow = $('<div>').addClass('row mb-3');
    tempStorage.attributes.forEach((attr, attrIndex) => {
        const col = $('<div>').addClass('col-md');
        const formGroup = $('<div>').addClass('form-group');
        const label = $('<label>').addClass('form-label').text(attr.name);
        const select = $('<select>').addClass('form-select')
            .attr('name', `variations[${index}][${attr.id}]`);

        // Add all possible values for each attribute
        tempStorage.selectedValues[attr.id].forEach(val => {
            const option = $('<option>')
                .val(val.id)
                .text(val.value);
            
            if (combination && combination[attrIndex].valueId === val.id) {
                option.prop('selected', true);
            }
            
            select.append(option);
        });
        
        formGroup.append(label, select);
        col.append(formGroup);
        selectsRow.append(col);
    });

    // Details Row
    const detailsRow = $('<div>').addClass('row g-3');
    
    // SKU
    const skuCol = $('<div>').addClass('col-md-4').append(
    $('<div>').addClass('form-group').append(
        $('<label>').addClass('form-label').text('SKU'),
        $('<div>').addClass('input-group').append(
            $('<span>').addClass('input-group-text').text('SKU-'),
            $('<input>').addClass('form-control')
                .attr('type', 'text')
                .attr('name', `variations[${index}][sku]`)
                .attr('placeholder', 'Nhập mã')
        )
    )
);

    // Price
    const priceCol = $('<div>').addClass('col-md-4').append(
        $('<div>').addClass('form-group').append(
            $('<label>').addClass('form-label').text('Giá (VNĐ)'),
            $('<input>').addClass('form-control')
                .attr('type', 'number')
                .attr('min', '0')
                .attr('step', '1000')
                .attr('name', `variations[${index}][price]`)
                .attr('placeholder', 'Nhập giá')
        )
    );

    // Stock
    const stockCol = $('<div>').addClass('col-md-4').append(
        $('<div>').addClass('form-group').append(
            $('<label>').addClass('form-label').text('Số lượng'),
            $('<input>').addClass('form-control')
                .attr('type', 'number')
                .attr('min', '0')
                .attr('name', `variations[${index}][stock]`)
                .attr('placeholder', 'Nhập số lượng')
        )
    );

    // Image Upload
    const imageCol = $('<div>').addClass('col-12 mt-3').append(
        $('<div>').addClass('form-group').append(
            $('<label>').addClass('form-label').text('Hình ảnh biến thể'),
            $('<input>').addClass('form-control variation-image')
                .attr('type', 'file')
                .attr('accept', 'image/*')
                .attr('name', `variations[${index}][image]`),
            $('<div>').addClass('image-preview mt-2')
        )
    );

    detailsRow.append(skuCol, priceCol, stockCol, imageCol);
    cardBody.append(selectsRow, detailsRow);
    body.append(cardBody);
    variationCard.append(header, body);
    
    return variationCard;
}

function generateVariationSelects() {
    const container = $('#variationsContainer');
    container.empty();

    const combinations = generateCombinations();
    combinations.forEach((combination, index) => {
        const variationCard = createVariationCard(index, combination);
        container.append(variationCard);
    });
}
// Add manual variation handler
$('#addManualVariation').click(() => {
    const newIndex = $('#variationsContainer .card').length;
    const manualVariation = createVariationCard(newIndex, true);
    $('#variationsContainer').append(manualVariation);
});

// For auto-generated variations
function generateVariationSelects() {
    const container = $('#variationsContainer');
    container.empty();
    const combinations = generateCombinations(tempStorage.selectedValues);
    combinations.forEach((combination, index) => {
        const variationCard = createVariationCard(index, combination);
        container.append(variationCard);
    });
}




function generateCombinations() {
    let combinations = [[]];
    let usedCombinations = new Set();
    
    tempStorage.attributes.forEach(attr => {
        const values = tempStorage.selectedValues[attr.id];
        const newCombinations = [];
        
        combinations.forEach(combo => {
            values.forEach(val => {
                const newCombo = [...combo, {
                    attributeId: attr.id,
                    attributeName: attr.name,
                    valueId: val.id,
                    value: val.value
                }];
                
                const comboKey = JSON.stringify(newCombo.map(c => c.valueId));
                if (!usedCombinations.has(comboKey)) {
                    usedCombinations.add(comboKey);
                    newCombinations.push(newCombo);
                }
            });
        });
        
        combinations = newCombinations;
    });

    return combinations;
}



$('#saveVariations').click(function() {
    const variations = [];
    
    $('#variationsContainer .card').each(function(index) {
        const variation = {
            attributes: {},
            sku: $(this).find('input[name^="variations["][name$="[sku]"]').val(),
            price: $(this).find('input[name^="variations["][name$="[price]"]').val(),
            stock: $(this).find('input[name^="variations["][name$="[stock]"]').val()
        };

        $(this).find('select').each(function() {
            const attributeId = $(this).attr('name').match(/\[(\d+)\]/)[1];
            variation.attributes[attributeId] = $(this).val();
        });

        variations.push(variation);
    });

    tempStorage.variations = variations;
    console.log('Saved variations:', tempStorage.variations);
    
    Swal.fire('Thành công', 'Đã lưu thay đổi biến thể', 'success');
})



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


        // Attribute Select Handler
        $('#attributeSelect').change(function() {
            const attributeId = $(this).val();
            if (attributeId) {
                // Show the selected attribute's container
                $(`#attribute_${attributeId}`).show();
                // Disable the selected option to prevent duplicate selection
                $(this).find(`option[value="${attributeId}"]`).prop('disabled', true);
                // Reset select to default option
                $(this).val('');
            }
        });

        // Remove attribute button handler
        $('.remove-attribute').click(function() {
            const attributeId = $(this).data('attribute-id');
            // Hide the attribute container
            $(`#attribute_${attributeId}`).hide();
            // Uncheck all checkboxes within this attribute
            $(`#attribute_${attributeId}`).find('input[type="checkbox"]').prop('checked', false);
            // Enable the option in the select dropdown
            $('#attributeSelect').find(`option[value="${attributeId}"]`).prop('disabled', false);
        });

        // Handle attribute value selection
        $('.attribute-values input[type="checkbox"]').change(function() {
            const attributeId = $(this).closest('.attribute-item').data('attribute-id');
            const selectedValues = [];

            // Collect all checked values for this attribute
            $(`#attribute_${attributeId} input[type="checkbox"]:checked`).each(function() {
                selectedValues.push($(this).val());
            });

            // Store selected values in tempAttributeValues array
            if (selectedValues.length > 0) {
                tempAttributeValues[attributeId] = selectedValues;
            } else {
                delete tempAttributeValues[attributeId];
            }
        });



        // Thuộc tính
        // document.getElementById('value-select').addEventListener('change', function() {
        //     const select = document.getElementById('value-select');
        //     const selectedTagsContainer = document.getElementById('selected-tags');
        //     const selectedOptions = Array.from(select.selectedOptions);

        //     // Add selected options as tags
        //     selectedOptions.forEach(option => {
        //         if (!Array.from(selectedTagsContainer.children).some(tag => tag.innerText.includes(option
        //                 .value))) {
        //             const tag = document.createElement('span');
        //             tag.className = 'badge bg-light text-dark border d-flex align-items-center gap-1';
        //             tag.innerText = option.value;

        //             // Close button to remove tag
        //             const closeButton = document.createElement('button');
        //             closeButton.type = 'button'; // Using button type for the close button
        //             closeButton.className = 'btn-close btn-close-sm';
        //             closeButton.onclick = () => removeTag(tag, option);
        //             tag.appendChild(closeButton);

        //             selectedTagsContainer.appendChild(tag);
        //         }
        //     });

        //     // Disable selected options
        //     Array.from(select.options).forEach(option => {
        //         option.disabled = selectedOptions.some(selectedOption => selectedOption.value === option
        //             .value);
        //     });
        // });

        // function removeTag(tag, option) {
        //     const select = document.getElementById('value-select');
        //     const optionToRemove = Array.from(select.options).find(opt => opt.value === option.value);
        //     optionToRemove.selected = false; // Deselect the option
        //     tag.remove(); // Remove the tag element
        //     updateDisabledOptions();
        // }

        // function selectAll() {
        //     const select = document.getElementById('value-select');
        //     Array.from(select.options).forEach(option => option.selected = true);
        //     document.getElementById('value-select').dispatchEvent(new Event('change'));
        // }

        // function deselectAll() {
        //     const select = document.getElementById('value-select');
        //     Array.from(select.options).forEach(option => option.selected = false);
        //     document.getElementById('value-select').dispatchEvent(new Event('change'));
        // }

        // function createValue() {
        //     const newValue = prompt("Nhập giá trị mới:");
        //     if (newValue) {
        //         const select = document.getElementById('value-select');
        //         const newOption = document.createElement('option');
        //         newOption.value = newValue;
        //         newOption.innerText = newValue;
        //         select.appendChild(newOption);
        //     }
        // }


        function updateDisabledOptions() {
            const select = document.getElementById('value-select');
            const selectedTags = Array.from(document.getElementById('selected-tags').children);
            const selectedValues = selectedTags.map(tag => tag.innerText.replace(/\s×$/, ''));

            Array.from(select.options).forEach(option => {
                option.disabled = selectedValues.includes(option.value);
            });
        }



        // attributeSelect


        const containerAccords = document.getElementById('container-accords');
        const attributeSelectTag = document.getElementById('attributeSelect');

        const renderAccord = (title) => {
            const uniqueId = title.toLowerCase().replace(/\s+/g, '-'); // Tạo ID duy nhất từ tiêu đề

            // Tạo HTML cho accordion mới
            const newAccord = `
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading-${uniqueId}">
                <button class="accordion-button" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapse-${uniqueId}" aria-expanded="true"
                    aria-controls="collapse-${uniqueId}">
                    ${title}
                </button>
            </h2>
            <div id="collapse-${uniqueId}"
                class="accordion-collapse collapse show"
                aria-labelledby="heading-${uniqueId}"
                data-bs-parent="#container-accords">
                <div class="accordion-body">
                    <!-- Tags for ${title} -->
                    <div id="selected-tags-${uniqueId}"
                        class="d-flex flex-wrap gap-2 mb-2">
                        <!-- Selected tags for ${title} will appear here -->
                    </div>

                    <!-- Select dropdown for ${title} -->
                    <select id="value-select-${uniqueId}" class="form-select" size="5">
                      
                    </select>
                </div>
            </div>
        </div>
    `;

            // Thêm HTML vào container
            containerAccords.innerHTML += newAccord;
        };



        attributeSelectTag.addEventListener('change', function(e) {
            const tagId = e.target.value;
            console.log(e.target.name);
            const selectedOption = this.options[this.selectedIndex]; // Lấy option đang được chọn
            const text = selectedOption.textContent || selectedOption.innerText;
            renderAccord(text);


        });
    </script>
@endpush
