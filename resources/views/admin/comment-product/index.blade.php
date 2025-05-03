@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.blogs.trash') }}" class="btn btn-secondary me-2">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Trash
                        </a>
                    </div>
                    <h4 class="page-title">Quản lý bình luận</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped w-100">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Sản phẩm</th>
                                    <th>Người dùng</th>
                                    <th>Bình luận</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($comments as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->product->name }}</td>
                                        <td>{{ $value->user->name }}</td>
                                        <td>{{ $value->comment }}</td>
                                        <td>{{ $value->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.comments-product.edit', $value->id) }}"><button
                                                        class="btn btn-sm btn-success me-1"><i
                                                            class="mdi mdi-comment-edit-outline"></i></button></a>
                                                <button class="btn btn-sm btn-danger delete-btn delete-comment"
                                                    data-id="{{ $value->id }}">
                                                    <i class="mdi mdi-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>STT</th>
                                    <th>Sản phẩm</th>
                                    <th>Người dùng</th>
                                    <th>Bình luận</th>
                                    <th>Ngày tạo</th>
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
    <script src="{{ asset('admin/api/comment.js') }}"></script>
@endpush
