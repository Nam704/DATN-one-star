@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="page-title">Phân quyền cho: {{ $role->name }}</h4>
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
                        <form action="{{ route('admin.roles.permissions.update', $role->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group mb-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="select-all">
                                    <label class="custom-control-label font-weight-bold" for="select-all">Chọn tất cả quyền</label>
                                </div>
                            </div>

                            <div class="row">
                                @php
                                    $groupedPermissions = $permissions->groupBy('module');
                                @endphp

                                @foreach($groupedPermissions as $module => $modulePermissions)
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input module-checkbox" 
                                                        id="module_{{ $module }}" 
                                                        data-module="{{ $module }}">
                                                    <label class="custom-control-label font-weight-bold" for="module_{{ $module }}">
                                                        {{ ucfirst($module) }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @foreach($modulePermissions as $permission)
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input permission-checkbox" 
                                                            id="permission_{{ $permission->id }}" 
                                                            name="permissions[]" 
                                                            value="{{ $permission->id }}"
                                                            data-module="{{ $module }}"
                                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
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

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success">Cập nhật quyền</button>
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
    <script>
        $(document).ready(function() {
            // Select all checkboxes
            $('#select-all').on('change', function() {
                $('.permission-checkbox').prop('checked', $(this).is(':checked'));
                $('.module-checkbox').prop('checked', $(this).is(':checked'));
            });

            // Module checkboxes
            $('.module-checkbox').on('change', function() {
                var module = $(this).data('module');
                $('.permission-checkbox[data-module="' + module + '"]').prop('checked', $(this).is(':checked'));
                updateSelectAllCheckbox();
            });

            // Individual permission checkboxes
            $('.permission-checkbox').on('change', function() {
                var module = $(this).data('module');
                updateModuleCheckbox(module);
                updateSelectAllCheckbox();
            });

            // Update module checkbox status based on all permissions within that module
            function updateModuleCheckbox(module) {
                var allChecked = $('.permission-checkbox[data-module="' + module + '"]').length === 
                                $('.permission-checkbox[data-module="' + module + '"]:checked').length;
                $('#module_' + module).prop('checked', allChecked);
            }

            // Update select all checkbox based on all permissions
            function updateSelectAllCheckbox() {
                var allChecked = $('.permission-checkbox').length === $('.permission-checkbox:checked').length;
                $('#select-all').prop('checked', allChecked);
            }

            // Initialize module checkboxes state
            $('.module-checkbox').each(function() {
                var module = $(this).data('module');
                updateModuleCheckbox(module);
            });

            // Initialize select all checkbox state
            updateSelectAllCheckbox();
        });
    </script>
@endpush
