@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle me-1"></i>
                            Thêm mới
                        </a>
                        <a href="{{ route('admin.blogs.trash') }}" class="btn btn-secondary me-2">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Danh sách xóa
                        </a>
                    </div>
                    <h4 class="page-title">Danh sách tin tức</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped  w-100">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Danh mục</th>
                                    <th>Hình ảnh</th>
                                    <th>Tiêu đề</th>
                                    <th>Ngày đăng tải</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($blogs as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->category->name }}</td>
                                        <td><img src="{{ asset($value->thumbnail) }}" alt="err" height="60px"></td>
                                        <td>{{ $value->title }}</td>
                                        <td>{{ $value->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $value->status === 'published' ? 'success' : ($value->status === 'draft' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($value->status) }}
                                            </span>

                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.blogs.show', $value->id) }}">
                                                    <button type="button"
                                                        class="btn btn-secondary btn-sm btn-warning me-1">Chi tiết</button>
                                                </a>
                                                <a href="{{ route('admin.blogs.edit', $value->id) }}"><button
                                                        class="btn btn-sm btn-success me-1">Sửa</button></a>
                                                <button class="btn btn-sm btn-danger delete-btn"
                                                    data-id="{{ $value->id }}">
                                                    Tạm dừng
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>STT</th>
                                    <th>Danh mục</th>
                                    <th>Hình ảnh</th>
                                    <th>Tiêu đề</th>
                                    <th>Ngày đăng tải</th>
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
    <script src="{{ asset('admin/api/blog.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
@endpush
