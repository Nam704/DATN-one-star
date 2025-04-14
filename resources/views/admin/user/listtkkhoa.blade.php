@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="page-title-box">
                        <h4 class="page-title">Danh sách Tài khoản khóa</h4>
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
                            @foreach ($listTaiKhoan as $key => $value)
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
                                <a href="{{ route('admin.users.show', $value->id) }}">
                                            <button type="button" class="btn btn-sm btn-info me-1" title="Xem chi tiết">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                        </a>
                                    <!-- Nút mở khóa -->
                                    <form action="{{ route('admin.users.opentk', $value->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Bạn có muốn mở khóa tài khoản này không')"><i class="mdi mdi-lock-open"></i></button>
                                    </form>
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