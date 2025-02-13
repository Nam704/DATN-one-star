<<<<<<< HEAD

=======
>>>>>>> 7e4fffbd8e3ac0d6e2a89b68e60e3b1247343418
@extends('admin.layouts.layout')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Fixed Header</h4>


<<<<<<< HEAD
                    <a href="{{ route('admin.categories.addCategory') }}" type="button" class="btn btn-sm btn-primary">
=======
                    <a href="{{ route('admin.productvariant.addProductVariant') }}" type="button" class="btn btn-sm btn-primary">
>>>>>>> 7e4fffbd8e3ac0d6e2a89b68e60e3b1247343418
                        Add new
                        category</a>

                </div>

                <div class="card-body">

                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                            <tr>
                                <th>Stt</th>
                                <th>Name</th>
                                <td>Parent</td>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($categories as $key => $category)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{$category->name}}</td>
                                <td>{{ $category->parent->name ?? 'Không có danh mục cha' }}</td>
                                <td>{{$category->status}}</td>
                                <td>
                                    <a href="{{route('admin.categories.editCategory',$category->id)}}">
                                        <button type="button" class="btn btn-secondary btn-warning">Sửa</button>
                                    </a> |
                                    <form action="{{route('admin.categories.deleteCategory',$category->id)}}" class="d-inline" method="POST" onclick="return confirm('Ban co muon xoa khong')">
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
                                <th>Stt</th>
                                <th>Name</th>
                                <td>Parent</td>
                                <th>Status</th>
                                <th>Action</th>
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
<<<<<<< HEAD
@endpush
=======
@endpush
>>>>>>> 7e4fffbd8e3ac0d6e2a89b68e60e3b1247343418
