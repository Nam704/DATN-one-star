@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-primary btn-sm">Quay lại</a>
                    </div>
                    <h4 class="page-title">Bình luận đã xóa</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

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
                                                <form action="{{ route('admin.comments.restore', $comment->id) }}" method="POST" class="d-inline me-1" onsubmit="return confirm('Bạn có muốn khôi phục không?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">Khôi phục</button>
                                                </form>
                                                <form action="{{ route('admin.comments.delete', $comment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Chắc chắn xóa bình luận này vĩnh viễn?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Xóa vĩnh viễn</button>
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

