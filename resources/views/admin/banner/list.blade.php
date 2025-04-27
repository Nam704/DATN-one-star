@extends('admin.layouts.layout')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Danh sách Banner</h4>
                    <a href="{{ route('admin.banner.create') }}" type="button" class="btn btn-sm btn-primary">Thêm banner</a>
                </div>

                <div class="card-body">

                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Ảnh</th>
                                <th>Ngày bát đầu</th>
                                <th>Ngày kết thúc</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($banners as $key => $banner)
                            <tr>

                                <td>{{ $banner->title }}</td>
                                <td><img src="{{ asset('storage/' . $banner->image) }}" alt="err" height="60px"></td>
                                <td>{{ $banner->start_date }}</td>
                                <td>{{$banner->end_date}}</td>
                                <td>{{ (int) $banner->status === 1 ? 'Hoạt động' : 'Không hoạt động' }}</td>
                                <td>
                                    <a href="{{ route('admin.banner.edit',$banner->id) }}">
                                        <button type="button" class="btn btn-secondary btn-warning">Sửa</button>
                                    </a>
                                    <a href="{{ route('admin.banner.detail',$banner->id) }}"><button
                                            class="btn btn-info">Chi tiết</button></a>
                                    <form action="{{route('admin.banner.delete',$banner->id)}}"
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

                                <th>Tiêu đề</th>
                                <th>Ảnh</th>
                                <th>Ngày bát đầu</th>
                                <th>Ngày kết thúc</th>
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