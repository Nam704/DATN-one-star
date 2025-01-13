@extends('admin.layouts.layout')
@section('content')

<div class="container mt-5">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 mt-2">Thêm mới sản phẩm</h1>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf

        <!-- Product Information -->
        <div class="mb-3">
            <label for="name" class="form-label">Product Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            <div class="invalid-feedback">Please enter the product name.</div>
        </div>

        <div class="mb-3">
            <label for="id_brand" class="form-label">Brand:</label>
            <select class="form-select" id="id_brand" name="id_brand" required>
                <option value="" selected disabled>Select a brand</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('id_brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">Please select a brand.</div>
        </div>

        <div class="mb-3">
            <label for="id_category" class="form-label">Category:</label>
            <select class="form-select" id="id_category" name="id_category" required>
                <option value="" selected disabled>Select a category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('id_category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">Please select a category.</div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description :</label>
            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
            <div class="invalid-feedback">Please enter the product description.</div>
        </div>

        <div class="mb-3">
            <label for="image_primary" class="form-label">Primary Image:</label>
            <input class="form-control" type="file" id="image_primary" name="image_primary" required>
            <div class="invalid-feedback">Please upload a primary image.</div>
        </div>

        <!-- Variants -->
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

        <!-- Additional Images -->
        <div class="mb-4">
            <h3 class="mb-3">Additional Images</h3>
            <button type="button" class="btn btn-outline-secondary mb-3" onclick="addImage()">Add Image</button>
            <div id="additional-images"></div>
        </div>

        <!-- Attributes -->
        <div class="mb-4">
            <h3 class="mb-3">Attributes</h3>
            @foreach ($attributes as $attribute)
                <div class="mb-3">
                    <h5>{{ $attribute->name }}</h5>
                    @foreach ($attribute->values as $value)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="attribute-{{ $value->id }}" name="attributes[{{ $attribute->id }}][]" value="{{ $value->id }}" {{ is_array(old('attributes.' . $attribute->id)) && in_array($value->id, old('attributes.' . $attribute->id)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="attribute-{{ $value->id }}">{{ $value->value }}</label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>

@endsection

<script>
    // JavaScript for dynamic variants and images
    function addImage() {
    const imagesContainer = document.getElementById('additional-images');
    const maxImages = 4; // Số lượng tối đa cho phép
    const currentImageCount = imagesContainer.children.length; // Đếm số lượng hình ảnh hiện tại

    const imagesToAdd = Math.min(maxImages - currentImageCount, 4); // Số lượng ảnh cần thêm nhưng không vượt quá maxImages

    if (currentImageCount < maxImages) {
        for (let i = 0; i < imagesToAdd; i++) {
            const imageTemplate = `
                <div class="mb-3 d-flex align-items-center">
                    <input class="form-control me-2" type="file" name="additional_images[]">
                    <button type="button" class="btn btn-danger" onclick="removeImage(this)">Remove</button>
                </div>`;
            imagesContainer.insertAdjacentHTML('beforeend', imageTemplate);
        }
    } else {
        alert('Tối Đa Chỉ Thêm Được 4 Hình Ảnh.');
    }
}
    function removeVariant(button) {
        button.closest('.card').remove();
    }

    function addImage() {
    const imagesContainer = document.getElementById('additional-images');
    const maxImages = 4;  // Set the maximum number of images allowed
    const currentImageCount = imagesContainer.children.length;

    if (currentImageCount < maxImages) {
        const imageTemplate = `
            <div class="mb-3 d-flex align-items-center">
                <input class="form-control me-2" type="file" name="additional_images[]">
                <button type="button" class="btn btn-danger" onclick="removeImage(this)">Remove</button>
            </div>`;

        imagesContainer.insertAdjacentHTML('beforeend', imageTemplate);
    } else {
        alert('Tối Đa Chỉ Thêm Được 4 Hình Ảnh.');
    }
}

function removeImage(button) {
    button.closest('.mb-3').remove();
}

</script>

