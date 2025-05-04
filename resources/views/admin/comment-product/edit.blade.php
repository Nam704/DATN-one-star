@extends('admin.layouts.layout')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.comments-product.index') }}" class="btn btn-dark mb-2">
                            <i class="mdi mdi-arrow-left-thin"></i>
                            Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card d-block">
                <div class="card-body">
                    <h4 class="card-title">{{ $product->name }}</h4>
                    <h6 class="card-subtitle text-muted">{{ $product->Brand->name }} -
                        {{ $product->Category->name }}</h6>
                </div>
                <img class="img-fluid" src="{{ asset($product->image_primary) }}" alt="image">
                <div class="card-body">
                    <p class="card-text">{{ $product->description }}</p>
                </div> <!-- end card-body-->

                <div class="card-body">
                    <h4 class="card-title">Bình luận</h4>

                    <div style="max-height: 300px;" data-simplebar>

                        {{-- Nếu là bình luận con thì hiển thị bình luận cha trước --}}
                        @if ($comment->parent)
                            <div class="col-5 mess-parent">
                                <div class="card bg-body-secondary bd-css">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon">
                                                <img style="width: 50px; height: 50px"
                                                    src="/admin/assets/images/user-201.png" class="img-fluid rounded-circle"
                                                    alt="img" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold fs-14">
                                                {{ $comment->parent->user->name ?? 'Ẩn danh' }}
                                                <small
                                                    class="fw-normal text-muted float-end ms-1">{{ $comment->parent->created_at->diffForHumans() }}</small>
                                            </h5>
                                            <p class="mb-1">{{ $comment->parent->comment }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Hiển thị bình luận hiện tại --}}
                        <div class="col-5 mess-parent mt-3">
                            <div class="card bg-body-secondary bd-css">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="notify-icon">
                                            <img style="width: 50px; height: 50px" src="/admin/assets/images/user-201.png"
                                                class="img-fluid rounded-circle" alt="img" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 text-truncate ms-2">
                                        <h5 class="noti-item-title fw-semibold fs-14">
                                            {{ $comment->user->name ?? 'Ẩn danh' }}
                                            <small
                                                class="fw-normal text-muted float-end ms-1">{{ $comment->created_at->diffForHumans() }}</small>
                                        </h5>
                                        <p class="mb-1">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hiển thị các trả lời nếu có --}}
                        @if ($comment->replies->count())
                            @foreach ($comment->replies as $reply)
                                <div class="col-5 mess-replies mt-3">
                                    <div class="card bg-body-secondary bd-css">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="notify-icon">
                                                    <img style="width: 50px; height: 50px"
                                                        src="/admin/assets/images/user-201.png"
                                                        class="img-fluid rounded-circle" alt="img" />
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 text-truncate ms-2">
                                                <h5 class="noti-item-title fw-semibold fs-14">
                                                    {{ $reply->user->name ?? 'Ẩn danh' }}
                                                    <small
                                                        class="fw-normal text-muted float-end ms-1">{{ $reply->created_at->diffForHumans() }}</small>
                                                </h5>
                                                <p class="mb-1">{{ $reply->comment }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div> <!-- end card-body-->
                </div> <!-- end card-->

                <div class="card-body">
                    <div class="col-5">
                        <form action="{{ route('admin.comments-product.reply', $comment->id) }}" method="POST">
                            @csrf
                            <label for="reply">Trả lời bình luận</label>
                            <input name="comment" class="form-control" required>
                            <button type="submit" class="btn btn-info mt-2">Gửi</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <form id="delete-comment-form-{{ $comment->id }}" method="POST"
            action="{{ route('admin.comments-product.destroy', $comment->id) }}">
            @csrf
            @method('DELETE')
            {{-- <button type="submit" class="text-danger">Xóa</button> --}}
        </form>
        <!-- /.container-fluid -->
    @endsection
    @push('styles')
    @endpush

    @push('scripts')
        <script src="{{ asset('admin/api/comment.js') }}"></script>
    @endpush
