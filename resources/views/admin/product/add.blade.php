@extends('admin.layouts.layout')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 mt-2">Thêm mới sản phẩm</h1>
    </div>
    
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{route('admin.products.addPostProduct')}}" method="post" enctype="multipart/form-data" class="form">
                        @csrf
                        <!-- Tên sản phẩm -->
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Tên sản phẩm:</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên sản phẩm" required>
                        </div>

                        <!-- Trình soạn thảo Quill -->
                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Mô tả sản phẩm:</label>
                            <div id="snow-editor" style="height: 300px; border: 1px solid #ddd; border-radius: 5px;"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Danh mục sản phẩm</h6>
                </div>
                <div class="card-body">
                    <!-- Danh sách danh mục -->
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

                    <!-- Thêm danh mục mới -->
                    <div class="form-group mt-3">
                        <label for="new_category" class="font-weight-bold">Thêm danh mục mới:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="new_category" name="new_category" placeholder="Nhập danh mục mới">
                            <div class="input-group-append">
                                <button type="button" id="add_category" class="btn btn-primary">Thêm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit -->
    <div class="d-flex justify-content-end">
        <button type="submit" name="submit" class="btn btn-success mr-2">Lưu sản phẩm</button>
        <a href="{{ route('admin.products.listProduct')}}" class="btn btn-secondary ms-2">Quay lại</a>
    </div>
</div>
<!-- /.container-fluid -->
@endsection

@push('styles')
<!-- Quill Editor CSS -->
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
<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    // Khởi tạo Quill Editor
    var quill = new Quill('#snow-editor', {
        theme: 'snow',
        placeholder: 'Nhập nội dung sản phẩm tại đây...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'], // Text formatting
            [{ 'header': [1, 2, 3, false] }], // Headers
            [{ 'list': 'ordered'}, { 'list': 'bullet' }], // Lists
            ['link', 'image', 'video'], // Media
            [{ 'align': [] }], // Add alignment buttons
            ['clean'] // Clear formatting
            ]
        }
    });

    // Lấy nội dung từ Quill Editor trước khi submit form
    document.querySelector('form').addEventListener('submit', function(e) {
        const editorContent = quill.root.innerHTML;
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'description'; // Tên field để gửi lên server
        hiddenInput.value = editorContent;
        this.appendChild(hiddenInput);
    });

    // Thêm danh mục mới
    document.getElementById('add_category').addEventListener('click', function() {
        const newCategory = document.getElementById('new_category').value.trim();
        if (newCategory) {
            const categoryList = document.querySelector('.form-group .border');
            const newCheckbox = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="category_${newCategory}" name="categories[]" value="${newCategory}">
                    <label class="form-check-label" for="category_${newCategory}">${newCategory}</label>
                </div>`;
            categoryList.insertAdjacentHTML('beforeend', newCheckbox);
            document.getElementById('new_category').value = '';
        } else {
            alert('Vui lòng nhập tên danh mục!');
        }
    });
</script>
@endpush
