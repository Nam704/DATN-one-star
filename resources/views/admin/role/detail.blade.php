@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid">
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Chi tiết quyền: {{ $role->name }}</h4>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                    <i class="mdi mdi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- Permissions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="form-group">
                        <label>Tên quyền:</label>
                        <input type="text" class="form-control" value="{{ $role->name }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Danh sách phân quyền:</label>
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
                                                <div class="form-check">
                                                    <input type="checkbox"
                                                        class="form-check-input"
                                                        id="permission_{{ $permission->id }}"
                                                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                        disabled>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
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

                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<x-admin.dashboard-styles />
@endpush
@push('scripts')
<x-admin.dashboard-scripts />
@endpush
