@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="profile-bg-picture" style="background-image:url('/admin/assets/images/bg-profile.jpg')">
                <span class="picture-bg-overlay"></span>
                <!-- overlay -->
            </div>
            <!-- meta -->
            <div class="profile-user-box">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="profile-user-img">
                            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('admin/assets/images/user-201.png') }}"
                                alt="img" class="avatar-lg rounded-circle">
                        </div>
                        <div class="">
                            <h4 class="mt-4 fs-17 ellipsis">{{ $user->name }}</h4>
                            <p class="font-13"> Vai trò : {{$user->role->name}}</p>
                            <p class="font-13"> Trạng thái : <span class="badge bg-info-subtle text-info">{{$user->status}}</span> </p>
                            <p class="text-muted mb-0"><small>Tham gia : {{ $user->created_at->format('d/m/Y') }}</small></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <div class="page-title-right">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-dark">
                                    <i class="mdi mdi-arrow-left-thin"></i>
                                    Back
                                </a>
                            </div>
                            <a class="btn btn-soft-info" href="{{ route('admin.users.chart_user', $user->id) }}"> <i class="mdi mdi-chart-areaspline fs-18 me-1 lh-1"></i>Thống kê</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ meta -->
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-sm-12">
            <div class="card p-0">
                <div class="card-body p-0">
                    <div class="profile-content">
                        <ul class="nav nav-underline nav-justified gap-0">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#aboutme" type="button" role="tab" aria-controls="home"
                                    aria-selected="true" href="#aboutme">Thông tin</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" data-bs-target="#projects"
                                    type="button" role="tab" aria-controls="home" aria-selected="true"
                                    href="#projects">Đơn hàng</a></li>
                        </ul>


                        <div class="tab-content m-0 p-4">
                            <div class="tab-pane active" id="aboutme" role="tabpanel" aria-labelledby="home-tab"
                                tabindex="0">
                                {{-- Thông tin cá nhân --}}
                                <div class="profile-desk">


                                    <h5 class="mt-4 fs-17 text-dark">Thông tin tài khoản</h5>
                                    <table class="table table-condensed mb-0 border-top">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Tên tài khoản</th>
                                                <td class="ng-binding">
                                                    {{ $user->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Email</th>
                                                <td class="ng-binding">
                                                    {{ $user->email }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <th scope="row">Số điện thoại</th>
                                                <td class="ng-binding">{{ $user->phone }}</td>
                                            </tr>

                                            <tr>
                                                <th scope="row">Địa chỉ</th>
                                                <td class="ng-binding">
                                                    @foreach ($user->addresses as $address)
                                                    <p>
                                                        {{ $address->address_detail }}
                                                        - {{ $address->ward->name ?? 'Chưa có xã' }}
                                                        - {{ $address->ward->district->name ?? 'Chưa có huyện' }}
                                                        -
                                                        {{ $address->ward->district->province->name ?? 'Chưa có tỉnh' }}

                                                        @if ($address->is_default)
                                                        <strong>( Mặc định )</strong>
                                                        @endif
                                                    </p>
                                                    @endforeach
                                                </td>
                                            </tr>


                                        </tbody>
                                    </table>
                                </div> <!-- end profile-desk -->
                            </div> <!-- about-me -->
                            <!-- Đơn hàng -->
                            <div id="projects" class="tab-pane">
                                <div class="row m-t-10">
                                    <div class="col-md-12">
                                        <div class="card-body">
                                            <table id="fixed-header-datatable"
                                                class="table table-striped dt-responsive nowrap table-striped  w-100">
                                                <thead>
                                                    <tr>
                                                        <th>STT</th>
                                                        <th>Mã đơn hàng</th>
                                                        <th>Ngày đặt hàng</th>
                                                        <th>Tổng tiền</th>
                                                        <th>Trạng thái</th>
                                                        <th>Hành động</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($order as $key => $value)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $value->code }}</td>
                                                        <td>{{ $value->created_at->format('d/m/Y') }}</td>
                                                        <td>{{ $value->total }}</td>
                                                        <td>

                                                            <span class="badge rounded-pill {{ $value->orderStatus->color ?? 'text-dark' }}">
                                                                {{ $value->orderStatus->name ?? 'Không xác định' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.orders.detail', $value->id) }}">
                                                                <button type="button"
                                                                    class="btn btn-secondary btn-sm btn-info me-1"><i
                                                                        class="mdi mdi-eye me-1"></i>Xem chi tiết</button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach


                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>STT</th>
                                                        <th>Mã đơn hàng</th>
                                                        <th>Ngày đặt hàng</th>
                                                        <th>Tổng tiền</th>
                                                        <th>Trạng thái</th>
                                                        <th>Hành động</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div> <!-- end card body-->
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

</div>
@endsection

@push('styles')
<x-admin.data-table-styles />
@endpush

@push('scripts')
<x-admin.data-table-scripts />
@endpush