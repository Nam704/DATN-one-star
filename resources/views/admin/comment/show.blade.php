@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.comments.index') }}" class="btn btn-primary btn-sm">Quay lại</a>
                    </div>
                    <h4 class="page-title">Chi tiết bình luận</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Thông tin người dùng</h5>
                                <p><strong>Tên người dùng:</strong> {{ $comment->user->name }}</p>
                                <p><strong>Email:</strong> {{ $comment->user->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5>Thông tin sản phẩm</h5>
                                <p><strong>Tên sản phẩm:</strong> {{ $comment->product->name }}</p>
                            </div>
                        </div>

                        <h5>Nội dung bình luận</h5>
                        <p>{{ $comment->comment }}</p>

                        <h5>Số sao</h5>
                        <p>{{ $comment->rating }} / 5</p>

                        <h5>Trạng thái</h5>
                        <p>{{ ucfirst($comment->status) }}</p>

                        <h5>Ngày bình luận</h5>
                        <p>{{ $comment->created_at->format('d/m/Y H:i') }}</p>

                        @if($comment->status === 'pending')
                            <div class="btn-group">
                                <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" class="d-inline me-1" onclick="return confirm('Bạn có muốn duyệt không?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                </form>
                                <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST" class="d-inline" onclick="return confirm('Bạn có muốn từ chối    không?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">Từ chối</button>
                                </form>
                            </div>
                        @endif
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

