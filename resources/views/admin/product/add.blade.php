@extends('admin.layouts.layout')
@section('content')
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
                    <form action="{{route('admin.products.addPostProduct')}}" method="post" enctype="multipart/form-data" class="form">
                        @csrf

                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Tên sản phẩm:</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên sản phẩm" required>
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Mô tả sản phẩm:</label>
                            <div id="snow-editor" style="height: 300px; border: 1px solid #ddd; border-radius: 5px;"></div>
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
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="category_{{ $category->id }}" name="categories[]" value="{{ $category->id }}">
                                <label class="form-check-label" for="category_{{ $category->id }}">{{ $category->name }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Add New Category Section -->
                    <div class="form-group mt-3">
                        <label for="new_category_name" class="font-weight-bold">Thêm danh mục mới:</label>
                        <div id="add-category-section">
                            <!-- Initially Hidden Input Fields -->
                            <input type="text" class="form-control mb-2" id="new_category_name" name="new_category_name" placeholder="Tên danh mục mới" style="display: none;">
                            <select class="form-control mb-2" id="parent_category" name="parent_category" style="display: none;">
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
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success mr-2">Lưu sản phẩm</button>
            <a href="{{ route('admin.products.listProduct')}}" class="btn btn-secondary ms-2">Quay lại</a>
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


    document.querySelector('form').addEventListener('submit', function(e) {
        const editorContent = quill.root.innerHTML;
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'description';
        hiddenInput.value = editorContent;
        this.appendChild(hiddenInput);
    });


    document.getElementById('show_add_category').addEventListener('click', function () {
    // Show the input fields for adding a new category
    document.getElementById('new_category_name').style.display = 'block';
    document.getElementById('parent_category').style.display = 'block';
    document.getElementById('confirm_add_category').style.display = 'block';
    this.style.display = 'none'; // Hide the "+ Add new category" button
});

document.getElementById('confirm_add_category').addEventListener('click', function () {
    const newCategoryName = document.getElementById('new_category_name').value.trim();
    const parentCategory = document.getElementById('parent_category').value;

    if (!newCategoryName) {
        alert('Vui lòng nhập tên danh mục!');
        return;
    }

    // Gửi yêu cầu AJAX tới server
    fetch("{{ route('admin.categories.addCategory') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            name: newCategoryName,
            parent_id: parentCategory || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Thêm danh mục mới vào danh sách checkbox
            const categoryList = document.querySelector('.form-group .border');
            const newCheckbox = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="category_${data.category.id}" name="categories[]" value="${data.category.id}">
                    <label class="form-check-label" for="category_${data.category.id}">${data.category.name}</label>
                </div>`;
            categoryList.insertAdjacentHTML('beforeend', newCheckbox);

            // Reset input
            document.getElementById('new_category_name').value = '';
            document.getElementById('parent_category').value = '';
            alert('Thêm danh mục thành công!');
        } else {
            alert('Thêm danh mục thất bại!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi thêm danh mục!');
    });
});


</script>
@endpush