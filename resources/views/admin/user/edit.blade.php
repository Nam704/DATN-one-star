@extends('admin.layouts.layout')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-dark">
                        <i class="mdi mdi-arrow-left-thin"></i>
                        Back
                    </a>
                </div>
                <h4 class="page-title">Sửa thông tin người dùng</h4>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT') <!-- Dùng PUT vì đây là một form cập nhật -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Thông tin cá nhân -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h4 class="header-title" style="margin-bottom: -20px">Thông tin cá nhân</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Tên người dùng :</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên người dùng" value="{{  $user->name }}">
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row mt-2">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="email" class="font-weight-bold">Email :</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" value="{{  $user->email }}">
                                    @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="phone" class="font-weight-bold">Số điện thoại :</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại" value="{{ $user->phone }}">
                                    @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label for="password" class="font-weight-bold">Mật khẩu :</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" >
                            @error('password')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Địa chỉ -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h4 class="header-title" style="margin-bottom: -20px">Địa chỉ</h4>
                    </div>
                    <div class="card-body">
                        <!-- Địa chỉ -->
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="province">Tỉnh / Thành phố</label>
                                <select class="form-control" id="province" name="province_id">
                                    <option value="">-- Chọn Tỉnh --</option>
                                    @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}"
                                        {{ old('province_id', optional(optional(optional(optional($user->address)->ward)->district)->province)->id) == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('province_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="district">Quận / Huyện</label>
                                <select class="form-control" id="district" name="district_id" {{ empty($districts) ? 'disabled' : '' }}>
                                    <option value="">-- Chọn Huyện --</option>
                                    @foreach ($districts as $district)
                                    <option value="{{ $district->id }}"
                                        {{ optional(optional($user->address)->ward->district)->id == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('district_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="ward">Xã / Phường</label>
                                <select class="form-control" id="ward" name="ward_id" {{ empty($wards) ? 'disabled' : '' }}>
                                    <option value="">-- Chọn Xã --</option>
                                    @foreach ($wards as $ward)
                                    <option value="{{ $ward->id }}"
                                        {{ optional($user->address)->id_ward == $ward->id ? 'selected' : '' }}>
                                        {{ $ward->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('ward_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="address_detail">Địa chỉ chi tiết</label>
                            <input type="text" class="form-control" name="address_detail" id="address_detail" placeholder="Số nhà, tên đường..." value="{{ old('address_detail', optional($user->address)->address_detail) }}">
                            @error('address_detail')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Phân quyền -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h4 class="header-title" style="margin-bottom: -20px">Phân quyền</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Chọn quyền hạn:</label>
                            <select class="form-control" id="id_role" name="id_role">
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('id_role', $user->id_role) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_role')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Ảnh người dùng -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h4 class="header-title" style="margin-bottom: -20px">Ảnh người dùng</h4>
                    </div>
                    <div class="card-body">
                        <input name="profile_image" type="file" id="userImage" class="form-control mb-3" accept="image/*">
                        <div id="imagePreview" class="text-center">
                            @if($user->profile_image)
                            <img src="{{ Storage::url($user->profile_image) }}" alt="User Image" style="max-width: 100px; max-height: 100px;">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success mr-2">Cập nhật tài khoản</button>
        </div>
    </form>
</div>
<!-- /.container-fluid -->
@endsection
@push('styles')
<!-- Quill css -->
<link href="{{ asset('admin/assets/vendor/quill/quill.core.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('admin/assets/vendor/quill/quill.snow.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<!-- Quill Editor js -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<!-- Quill Demo js -->
<script src="{{ asset('admin/assets/js/pages/quilljs.init.js') }}"></script>
<script src="{{ asset('admin/api/user.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const oldProvince = "{{ old('province_id', optional($user->address?->ward?->district?->province)->id) }}";
        const oldDistrict = "{{ old('district_id', optional($user->address?->ward?->district)->id) }}";
        const oldWard = "{{ old('ward_id', optional($user->address)->id_ward) }}";

        const provinceSelect = document.getElementById('province');
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');

        // Load Tỉnh
        fetch('/admin/address/provinces')
            .then(res => res.json())
            .then(data => {
                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh --</option>';
                data.forEach(p => {
                    provinceSelect.innerHTML += `<option value="${p.id}" ${oldProvince == p.id ? 'selected' : ''}>${p.name}</option>`;
                });

                if (oldProvince) {
                    provinceSelect.value = oldProvince;
                    loadDistricts(oldProvince);
                }
            });

        // Khi chọn Tỉnh
        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            districtSelect.innerHTML = '<option value="">-- Chọn Huyện --</option>'; // Reset ô Quận
            wardSelect.innerHTML = '<option value="">-- Chọn Xã --</option>'; // Reset ô Xã
            wardSelect.disabled = true; // Vô hiệu hóa ô Xã ban đầu

            // Làm mới các giá trị Quận và Xã khi chọn Tỉnh mới
            if (provinceId) {
                districtSelect.disabled = false; // Kích hoạt ô Quận
                loadDistricts(provinceId); // Tải danh sách Quận theo Tỉnh đã chọn
            } else {
                districtSelect.disabled = true; // Vô hiệu hóa ô Quận nếu không chọn Tỉnh
            }
        });

        // Khi chọn Quận
        districtSelect.addEventListener('change', function() {
            const districtId = this.value;
            wardSelect.innerHTML = '<option value="">-- Chọn Xã --</option>'; // Reset ô Xã

            if (districtId) {
                wardSelect.disabled = false; // Kích hoạt ô Xã khi chọn Quận
                loadWards(districtId); // Tải danh sách Xã theo Quận đã chọn
            } else {
                wardSelect.disabled = true; // Vô hiệu hóa ô Xã nếu không chọn Quận
            }
        });

        // Tải danh sách Quận theo Tỉnh
        function loadDistricts(provinceId) {
            fetch(`/admin/address/districts/${provinceId}`)
                .then(res => res.json())
                .then(data => {
                    districtSelect.innerHTML = '<option value="">-- Chọn Huyện --</option>';
                    data.forEach(d => {
                        districtSelect.innerHTML += `<option value="${d.id}" ${oldDistrict == d.id ? 'selected' : ''}>${d.name}</option>`;
                    });
                    districtSelect.disabled = false; // Kích hoạt ô Quận
                });
        }

        // Tải danh sách Xã theo Quận
        function loadWards(districtId) {
            fetch(`/admin/address/wards/${districtId}`)
                .then(res => res.json())
                .then(data => {
                    wardSelect.innerHTML = '<option value="">-- Chọn Xã --</option>';
                    data.forEach(w => {
                        wardSelect.innerHTML += `<option value="${w.id}" ${oldWard == w.id ? 'selected' : ''}>${w.name}</option>`;
                    });
                    wardSelect.disabled = false; // Kích hoạt ô Xã
                });
        }
    });
</script>


@endpush