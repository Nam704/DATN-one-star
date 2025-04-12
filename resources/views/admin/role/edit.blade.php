@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="page-title">Chỉnh sửa quyền: {{ $role->name }}</h4>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left"></i> Quay lại
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
                        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Tên quyền <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required {{ in_array($role->name, ['admin', 'employee', 'user']) ? 'readonly' : '' }}>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                @if(in_array($role->name, ['admin', 'employee', 'user']))
                                    <small class="text-muted">Không thể thay đổi tên quyền mặc định.</small>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Phân quyền</label>
                                <div class="row">
                                    @php
                                        $groupedPermissions = $permissions->groupBy('module');
                                    @endphp

                                    @foreach($groupedPermissions as $module => $modulePermissions)
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">{{ ucfirst($module) }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($modulePermissions as $permission)
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" 
                                                                id="permission_{{ $permission->id }}" 
                                                                name="permissions[]" 
                                                                value="{{ $permission->id }}"
                                                                {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                                                {{ $permission->display_name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Cập nhật</button>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
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
