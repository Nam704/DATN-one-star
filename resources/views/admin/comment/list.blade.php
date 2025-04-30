
@extends('admin.layouts.layout')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Danh sách bình luận</h4>
                    <a href="{{ route('admin.comments.listdelete') }}" class="btn btn-primary btn-sm">Danh sách xóa bình luận</a>
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
                                        <td>
                                            <div class="btn-group">
                                                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="d-inline" onclick="return confirm('Bạn có muốn xóa bình luận không?')">
                                                    @csrf
                                                    @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-warning me-1">Xóa</button>
                                                    </form>
                                                    <a href="{{ route('admin.comments.show', $comment->id) }}" class="btn btn-sm btn-info">Xem chi tiết</a>

                                                    <!-- <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Chắc chắn xóa bình luận này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Chi tiết</button>
                                                    </form> -->
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