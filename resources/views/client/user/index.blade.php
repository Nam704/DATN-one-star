@extends('client.layouts.home.layout')
@section('title', 'profile')
@section('content')
    <section class="main_content_area">
        <div class="container">
            <div class="account_dashboard">
                <div class="row">
                    <div class="col-sm-12 col-md-3 col-lg-3">
                        <!-- Nav tabs -->
                        <div class="dashboard_tab_button">
                            <ul role="tablist" class="nav flex-column dashboard-list" id="nav-tab">
                                <li> <a href="#orders" data-toggle="tab" class="nav-link active">Orders</a></li>
                                <li><a href="#address" data-toggle="tab" class="nav-link">Addresses</a></li>
                                <li><a href="#account-details" data-toggle="tab" class="nav-link">Account details</a></li>
                                <li><a href="{{ route('auth.logout') }}" class="nav-link">logout</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9 col-lg-9">
                        <!-- Tab panes -->
                        <div class="tab-content dashboard_content">

                            <div class="tab-pane fade show active" id="orders">
                                <h3>Orders</h3>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Order</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                <tr>
                                                    <td>{{ $order->code }}</td>
                                                    <td>{{ $order->created_at }}</td>
                                                    <td><span class="success">{{ $order->orderStatus->name }}</span></td>
                                                    <td>{{ $order->total }} </td>
                                                    <td><a href="cart.html" class="view">view</a></td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane row" id="address">
                                <div class="col-12 mb-20">
                                    <label></label>
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Chi tiết</th>
                                                            <th>Xã</th>
                                                            <th>Huyện</th>
                                                            <th>Tỉnh/Thành phố</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($addresses as $address)
                                                            <tr>

                                                                <td> {{ $address->address_detail }}</td>
                                                                <td>{{ $address->ward_name }}</td>
                                                                <td><span
                                                                        class="success">{{ $address->district_name }}</span>
                                                                </td>
                                                                <td>{{ $address->province_name }} </td>
                                                                <td>

                                                                    @if ($address->is_default == 0)
                                                                        <a href="#" class="view ">default</a> ||
                                                                        <a href="">delete</a>
                                                                    @else
                                                                        Is default
                                                                    @endif

                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @if (count($addresses) < 3)
                                            <div class="address-select row mb-2">
                                                <div class="col-md-4">
                                                    <select name="province" class="form-select" id="province">
                                                        <option value="" {{ old('province') ? 'selected' : '' }}>
                                                            Chọn tỉnh
                                                        </option>
                                                    </select>
                                                    @error('province')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <select name="district" class="form-select" id="district">
                                                        <option value="" {{ old('district') ? 'selected' : '' }}>
                                                            Chọn quận
                                                        </option>
                                                    </select>
                                                    @error('district')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <select name="ward" class="form-select" id="ward">
                                                        <option value="" {{ old('ward') ? 'selected' : '' }}>Chọn
                                                            phường
                                                        </option>
                                                    </select>
                                                    @error('ward')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 mt-2 row">
                                                    <div class="col-12 row">
                                                        <div class="col-10">
                                                            <input id="address_detail" class="form-control"
                                                                placeholder="House number and street name" type="text">
                                                        </div>
                                                        <div class="col-2">
                                                            <select name="" id="is_default" class=" form-control ">
                                                                <option value="0" selected>Phụ</option>
                                                                <option value="1">Mặc định</option>
                                                            </select>
                                                        </div>

                                                    </div>

                                                </div>
                                                <div class="col-12">
                                                    <button class="btn btn-primary" id="save_address">Save</button>
                                                </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="tab-pane fade" id="account-details">
                            <h3>Account details </h3>
                            <div class="login">
                                <div class="login_form_container">
                                    <div class="account_login_form">
                                        <form action="#" class="form-details">
                                            <div class="row row-cols-sm-2 row-cols-1">
                                                <div class="mb-2">
                                                    <input type="hidden" value="{{ $user->id }}" id="user_id">
                                                    <label class="form-label" for="FullName">Full Name</label>
                                                    <input type="text" name="name" value="{{ $user->name ?? '' }}"
                                                        id="FullName" class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="Email">Email</label>
                                                    <input type="email" value="{{ $user->email ?? '' }}" name="email"
                                                        id="Email" class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="web-url">Old password</label>
                                                    <input type="text" value="" name="old_password"
                                                        placeholder="Enter your old password" id="web-url"
                                                        class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="phone">Phone</label>
                                                    <input type="text" value="{{ $user->phone ?? '' }}"
                                                        name="phone" id="phone" placeholder="Enter your phone"
                                                        class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="new_password"> New
                                                        Password</label>
                                                    <input type="password" placeholder="8 - 15 Characters"
                                                        id="new_password" class="form-control" name="new_password">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="new_password_confirmation">Re-Password</label>
                                                    <input type="password" placeholder="8 - 15 Characters"
                                                        name="new_password_confirmation" id="new_password_confirmation"
                                                        class="form-control">
                                                </div>

                                            </div>
                                            <button class="btn btn-primary" type="submit"><i
                                                    class="ri-save-line me-1 fs-16 lh-1"></i> Save</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
@section('scripts')
    @vite('resources/js/address.js')
    <script src="{{ asset('client/api/accountDetails.js') }}"></script>
@endsection
