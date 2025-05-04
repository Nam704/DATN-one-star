@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Quản lý danh mục</h4>
                        <a href="{{ route('admin.categories.addCategory') }}" type="button" class="btn btn-sm btn-primary">
                            Thêm danh mục</a>

                        <a href="{{ route('admin.categories.trash') }}" type="button" class="btn btn-sm btn-success">
                            <i class="fas fa-trash-alt"></i>Thùng rác
                        </a>

                    </div>

                    <div class="card-body">

                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped  w-100">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên danh mục</th>
                                    <td>Danh mục cha</td>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($categories as $key => $category)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->parent->name ?? 'Không có danh mục cha' }}</td>
                                        <td>{{ $category->status }}</td>
                                        <td>
                                            <a href="{{ route('admin.categories.editCategory', $category->id) }}">
                                                <button type="button" class="btn btn-secondary btn-warning">Edit</button>
                                            </a> |
                                            <form action="{{ route('admin.categories.deleteCategory', $category->id) }}"
                                                class="d-inline" method="POST"
                                                onclick="return confirm('Bạn có muốn xóa không?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-secondary btn-danger ">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên danh mục</th>
                                    <td>Danh mục cha</td>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->
    </div>
@endsection
@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
@endpush
