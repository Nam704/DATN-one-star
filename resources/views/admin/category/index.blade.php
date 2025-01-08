@extends('admin.layouts.layout')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="mt-4">

                    <h1 class="text-center">Product Audit</h1>

                    @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                </div>


                <div class="card-body">
                    <div class="mb-3">
                        <a href="{{ route('admin.categories.addCategory') }}" class="btn btn-primary">Thêm mới</a>
                    </div>


                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped w-100">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Stt</th>
                                <th>Name</th>
                                <td>Parent</td>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Update At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @foreach($categories as $key => $category)
                        <tr>
                            <td class="text-center"><input type="checkbox" name="select[]" value=""></td>
                            <td>{{ $key+1 }}</td>
                            <td>{{$category->name}}</td>
                            <td>{{ $category->parent->name ?? 'Không có danh mục cha' }}</td>
                            <td>{{$category->status}}</td>
                            <td>{{$category->created_at}}</td>
                            <td>{{$category->updated_at}}</td>
                            <td>
                                <a href="{{route('admin.categories.editCategory',$category->id)}}">
                                    <button type="button" class="btn btn-secondary btn-sm">Sửa</button>
                                </a> |
                                <form action="{{route('admin.categories.deleteCategory',$category->id)}}" class="d-inline" method="POST" onclick="return confirm('Ban co muon xoa khong')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm ">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

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