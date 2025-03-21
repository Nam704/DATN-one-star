@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line align-middle me-1"></i>
                                    Add User
                                </a>
                                <a href="{{ route('admin.users.trash') }}" class="btn btn-secondary me-2">
                                    <i class="ri-delete-bin-line align-middle me-1"></i>
                                    Trash
                                </a>
                                <a class="btn btn-soft-info" href="{{ route('admin.users.charts') }}"> <i
                                        class="mdi mdi-chart-areaspline fs-18 me-1 lh-1"></i>Thống kê</a>
                            </div>
                            <h4 class="page-title">Users Management</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped  w-100">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên người dùng</th>
                                    <th>Hình ảnh</th>
                                    <th>Email</th>
                                    <th>Phân quyền</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($users as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td>
                                            <img src="{{ asset($value->profile_image ?? '/admin/assets/images/user-201.png') }}"
                                                alt="err" height="50px">
                                        </td>
                                        <td>{{ $value->email }}</td>
                                        <td>{{ $value->role->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $value->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($value->status) }}
                                            </span>

                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.users.show', $value->id) }}">
                                                    <button type="button"
                                                        class="btn btn-secondary btn-sm btn-warning me-1"><i
                                                            class="mdi mdi-eye"></i></button>
                                                </a>
                                                <a href="{{ route('admin.blogs.edit', $value->id) }}"><button
                                                        class="btn btn-sm btn-success me-1"><i
                                                            class="mdi mdi-comment-edit-outline"></i></button></a>
                                                <button class="btn btn-sm btn-danger delete-btn"
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
                                    <th>Danh mục</th>
                                    <th>Hình ảnh</th>
                                    <th>Tiêu đề</th>
                                    <th>Ngày đăng tải</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('admin/api/blog.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
@endpush
