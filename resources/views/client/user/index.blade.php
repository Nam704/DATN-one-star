@extends('client.layouts.home.layout')
@section('title','profile')
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
                                        <tr>
                                            <td>1</td>
                                            <td>May 10, 2018</td>
                                            <td><span class="success">Completed</span></td>
                                            <td>$25.00 for 1 item </td>
                                            <td><a href="cart.html" class="view">view</a></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>May 10, 2018</td>
                                            <td>Processing</td>
                                            <td>$17.00 for 1 item </td>
                                            <td><a href="cart.html" class="view">view</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane row" id="address">
                            <div class="col-12 mb-20">
                                <label>Street address <span>*</span></label>
                                <div class="row mb-2">
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
                                            <option value="" {{ old('ward')? 'selected' : '' }}>Chọn
                                                phường
                                            </option>
                                        </select>
                                        @error('ward')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>



                                </div>
                                <div class="col-12 mt-2">
                                    <input class="form-control" placeholder="House number and street name" type="text">
                                </div>

                            </div>
                        </div>
                        <div class="tab-pane fade" id="account-details">
                            <h3>Account details </h3>
                            <div class="login">
                                <div class="login_form_container">
                                    <div class="account_login_form">
                                        <form action="#">
                                            <div class="row row-cols-sm-2 row-cols-1">
                                                <div class="mb-2">
                                                    <label class="form-label" for="FullName">Full
                                                        Name</label>
                                                    <input type="text" name="name" value="{{ $user->name }}"
                                                        id="FullName" class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="Email">Email</label>
                                                    <input type="email" value="{{ $user->email }}" name="email"
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
                                                    <input type="text" value="{{ $user->phone ? $user->phone : "" }}"
                                                        name="phone" id="phone" placeholder="Enter your phone"
                                                        class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="new_password"> New Password</label>
                                                    <input type="password" placeholder="8 - 15 Characters"
                                                        id="new_password" class="form-control" name="new_password">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="re_password">Re-Password</label>
                                                    <input type="password" placeholder="8 - 15 Characters"
                                                        name="re_password" id="re_password" class="form-control">
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
@endsection