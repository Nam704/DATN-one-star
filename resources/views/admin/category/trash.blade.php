@extends('admin.layouts.layout')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Danh mục đã xóa</h4>
                    <a href="{{ route('admin.categories.listCategory') }}" class="btn btn-sm btn-primary">Quay lại danh sách</a>
                </div>

                <div class="card-body">
                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                            <tr>
                                <th>Stt</th>
                                <th>Tên</th>
                                <td>Danh mục cha</td>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
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
                                    <form action="{{route('admin.categories.restoreCategory',$category->id)}}"
                                        class="d-inline" method="POST"
                                        onclick="return confirm('Bạn có muốn khôi phục không?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success ">Khôi phục</button>
                                    </form> |
                                    <form action="{{ route('admin.categories.destroyPermanent', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn danh mục này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa vĩnh viễn</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th>Stt</th>
                                <th>Tên</th>
                                <td>Danh mục cha</td>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<x-admin.data-table-styles />
@endpush

@push('scripts')
<x-admin.data-table-scripts />
@endpush