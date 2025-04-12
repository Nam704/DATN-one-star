@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="page-title">Quản lý quyền</h4>
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-success btn-sm">
                                <i class="mdi mdi-plus"></i> Thêm quyền
                            </a>
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

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên quyền</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.roles.edit', $role->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <a href="{{ route('admin.roles.permissions', $role->id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="mdi mdi-key"></i>
                                                </a>
                                                @if (!in_array($role->name, ['admin', 'employee', 'user']))
                                                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                @endif
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
    <x-admin.dashboard-styles />
    </style>
@endpush
@push('scripts')
    <x-admin.dashboard-scripts />
@endpush