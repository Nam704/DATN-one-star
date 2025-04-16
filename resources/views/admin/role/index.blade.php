@extends('admin.layouts.layout')
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="page-title">Quản lý quyền nhân viên</h4>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Danh sách quyền</h4>

                    <table class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                            <tr>
                                <th>Stt</th>
                                <th>Tên quyền</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $key=>$role)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Actions">
                                        <a href="{{ route('admin.roles.detail', $role->id) }}"
                                            class="btn btn-outline-info btn-sm me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Chi tiết quyền">
                                            Xem
                                        </a>
                                        <a href="{{ route('admin.roles.permissions', $role->id) }}"
                                            class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Phân quyền">
                                            Phân quyền
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
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