

@extends('admin.layouts.layout')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Danh sách bình luận đã duyệt</h4>


                    <a href="{{ route('admin.comments.listdelete') }}" class="btn btn-primary btn-sm">Danh sách xóa</a>

                    <a href="{{ route('admin.comments.index') }}" class="btn btn-primary btn-sm">Quay lại</a>

                </div>

                <div class="card-body">

                <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Người dùng</th>
                                    <th>Sản phẩm</th>
                                    <th>Nội dung</th>
                                    <th>Số sao</th>
                                    <th>Ngày bình luận</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($comments as $key => $comment)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $comment->user->name }}</td>
                                        <td>{{ $comment->product->name }}</td>
                                        <td>{{ Str::limit($comment->comment, 50) }}</td>
                                        <td>{{ $comment->rating }}</td>
                                        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ ucfirst($comment->status) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.comments.show', $comment->id) }}" class="btn btn-sm btn-info me-1">Xem chi tiết</a>
                                                <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST" class="d-inline me-1" onclick="return confirm('Bạn có muốn từ chối không?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning">Từ chối</button>
                                                </form>

                                                <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Chắc chắn xóa bình luận này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>STT</th>
                                    <th>Người dùng</th>
                                    <th>Sản phẩm</th>
                                    <th>Nội dung</th>
                                    <th>Số sao</th>
                                    <th>Ngày bình luận</th>
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
