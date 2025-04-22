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
                                Thêm tài khoản
                            </a>
                            <a href="{{ route('admin.users.listtkkhoa') }}" class="btn btn-secondary ">
                                <i class="ri-delete-bin-line align-middle me-1"></i>
                                Tài khoản khóa
                            </a>
                            <a class="btn btn-soft-info" href="{{ route('admin.users.charts') }}"> <i
                                    class="mdi mdi-chart-areaspline fs-18 me-1 lh-1"></i>Thống kê</a>
                        </div>
                        <h4 class="page-title">Danh sách nhân viên</h4>
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
                                    <img src="{{ $value->profile_image ? asset('storage/' . $value->profile_image) : asset('admin/assets/images/user-201.png') }}"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
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
                                            <button type="button" class="btn btn-sm btn-info me-1" title="Xem chi tiết">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                        </a>

                                        <a href="{{ route('admin.users.edit', $value->id) }}">
                                            <button type="button" class="btn btn-sm btn-success me-1" title="Sửa người dùng">
                                                <i class="mdi mdi-account-edit-outline"></i>
                                            </button>
                                        </a>

                                        <a href="{{ route('admin.users.lock', $value->id) }}">
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="return confirm('Bạn có muốn khóa tài khoản này không')"> <i class="mdi mdi-lock me-1 "></i></button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                                <th>STT</th>
                                <th>Tên người dùng</th>
                                <th>Hình ảnh</th>
                                <th>Email</th>
                                <th>Phân quyền</th>
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