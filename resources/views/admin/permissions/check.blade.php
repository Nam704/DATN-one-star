@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="page-title">Kiểm tra quyền hạn</h4>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Thông tin người dùng</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Tên:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Vai trò:</strong> {{ $roleName }}</p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Các quyền của {{ $roleName }}</h5>
                    </div>
                    <div class="card-body">
                        @if(count($rolePermissions) > 0)
                            <ul class="list-group">
                                @foreach($rolePermissions as $permission)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $permission }}
                                        <a href="{{ route('admin.permissions.test', $permission) }}"
                                            class="btn btn-sm btn-primary test-permission" data-permission="{{ $permission }}">Kiểm
                                            tra</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-warning">
                                Vai trò này chưa được cấp quyền nào!
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">Tất cả quyền theo module</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="permissionsAccordion">
                            @foreach($groupedPermissions as $module => $modulePermissions)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $module }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $module }}" aria-expanded="false"
                                            aria-controls="collapse{{ $module }}">
                                            {{ ucfirst($module) }} ({{ count($modulePermissions) }})
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $module }}" class="accordion-collapse collapse"
                                        aria-labelledby="heading{{ $module }}" data-bs-parent="#permissionsAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-group">
                                                @foreach($modulePermissions as $permission)
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center 
                                                                                                                                                                    {{ in_array($permission->name, $rolePermissions) ? 'list-group-item-success' : '' }}">
                                                        {{ $permission->display_name }}
                                                        <span class="d-flex">
                                                            <small class="me-2 text-muted">{{ $permission->name }}</small>
                                                            <a href="{{ route('admin.permissions.test', $permission->name) }}"
                                                                class="btn btn-sm btn-primary test-permission"
                                                                data-permission="{{ $permission->name }}">Kiểm tra</a>
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">Kết quả kiểm tra quyền</h5>
                    </div>
                    <div class="card-body">
                        <div id="testResults" >
                            Chọn "Kiểm tra" bên cạnh quyền để xem kết quả
                        </div>
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
    <script>
        $(document).ready(function () {
            $('.test-permission').click(function (e) {
                e.preventDefault();
                var permission = $(this).data('permission');
                var url = $(this).attr('href');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        var resultHtml = '<div class="alert alert-' + (response.has_permission ? 'success' : 'danger') + '">';
                        resultHtml += '<h5>Kết quả kiểm tra quyền: ' + permission + '</h5>';
                        resultHtml += '<p><strong>Người dùng:</strong> ' + response.user + '</p>';
                        resultHtml += '<p><strong>Vai trò:</strong> ' + response.role + '</p>';
                        resultHtml += '<p><strong>Có quyền:</strong> ' + (response.has_permission ? 'Có' : 'Không') + '</p>';
                        resultHtml += '<p><strong>Tất cả các quyền:</strong></p>';
                        resultHtml += '<ul>';
                        response.all_permissions.forEach(function (perm) {
                            resultHtml += '<li>' + perm + '</li>';
                        });
                        resultHtml += '</ul>';

                        resultHtml += '</div>';

                        $('#testResults').html(resultHtml);
                    },
                    error: function (xhr) {
                        $('#testResults').html('<div class="alert alert-danger">Có lỗi xảy ra khi kiểm tra quyền</div>');
                    }
                });
            });
        });
    </script>
@endpush
