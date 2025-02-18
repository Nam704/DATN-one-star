@extends('client.layouts.layout')
@section('content')
    <section class="main_content_area">
        <div class="container">
            <div class="account_dashboard">
                <div class="row">
                    <!-- Sidebar (Menu) -->
                    <div class="col-sm-12 col-md-3 col-lg-3">
                        <div class="dashboard_tab_button">
                            <ul role="tablist" class="nav flex-column dashboard-list" id="nav-tab">
                                <li>
                                    <a href="#dashboard" data-toggle="tab" class="nav-link {{ !isset($status) ? 'active' : '' }}">
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="#orders" data-toggle="tab" class="nav-link {{ isset($status) ? 'active' : '' }}">
                                        Orders
                                    </a>
                                </li>
                                <li><a href="#downloads" data-toggle="tab" class="nav-link">Downloads</a></li>
                                <li><a href="#address" data-toggle="tab" class="nav-link">Addresses</a></li>
                                <li><a href="#account-details" data-toggle="tab" class="nav-link">Account details</a></li>
                                <li><a href="login.html" class="nav-link">Logout</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Main Content Area -->
                    <div class="col-sm-12 col-md-9 col-lg-9">
                        <div class="tab-content dashboard_content">
                            <!-- Dashboard Tab (các nội dung khác có thể giữ nguyên) -->
                            <div class="tab-pane fade {{ !isset($status) ? 'show active' : '' }}" id="dashboard">
                                <h3>Dashboard</h3>
                                <p>From your account dashboard, you can easily check &amp; view your <a
                                        href="#">recent orders</a>, manage your <a href="#">shipping and billing
                                        addresses</a> and <a href="#">Edit your password and account details.</a></p>
                            </div>

                            <!-- Orders Tab -->
                            <div class="tab-pane fade {{ isset($status) ? 'show active' : '' }}" id="orders">
                                <h3>Orders</h3>
                                <!-- Nút filter theo trạng thái -->
                                <div class="order-filters mb-3">
                                    <a href="{{ route('client.orders.index', ['status' => 'all']) }}"
                                        class="btn btn-default {{ $status == 'all' ? 'active' : '' }}">All</a>
                                    <a href="{{ route('client.orders.index', ['status' => 'pending']) }}"
                                        class="btn btn-default {{ $status == 'pending' ? 'active' : '' }}">Pending</a>
                                    <a href="{{ route('client.orders.index', ['status' => 'processing']) }}"
                                        class="btn btn-default {{ $status == 'processing' ? 'active' : '' }}">Processing</a>
                                    <a href="{{ route('client.orders.index', ['status' => 'shipped']) }}"
                                        class="btn btn-default {{ $status == 'shipped' ? 'active' : '' }}">Shipped</a>
                                    <a href="{{ route('client.orders.index', ['status' => 'completed']) }}"
                                        class="btn btn-default {{ $status == 'completed' ? 'active' : '' }}">Completed</a>
                                    <a href="{{ route('client.orders.index', ['status' => 'cancelled']) }}"
                                        class="btn btn-default {{ $status == 'cancelled' ? 'active' : '' }}">Cancelled</a>
                                </div>
                                <div class="table-responsive">
                                    @foreach($orders as $order)
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <strong>Đơn hàng #{{ $order->id }}</strong> | SĐT: {{ $order->phone_number }} | Tổng tiền: {{ number_format($order->total_amount, 2) }}<br>
                                            Trạng thái: {{ $order->orderStatus->name }}
                                        </div>
                                        <div class="card-body">
                                            <h5>Sản phẩm:</h5>
                                            @foreach($order->orderDetails as $detail)
                                                <div class="row mb-3">
                                                    <!-- Cột hình ảnh -->
                                                    <div class="col-md-3">
                                                        @if($detail->productVariant && $detail->productVariant->images && $detail->productVariant->images->count() > 0)
                                                            <img src="{{ $detail->productVariant->images->first()->url }}" alt="{{ $detail->product_name }}" class="img-fluid">
                                                        @else
                                                            <p>Không có hình ảnh</p>
                                                        @endif
                                                    </div>
                                                    <!-- Cột thông tin sản phẩm -->
                                                    <div class="col-md-9">
                                                        <p>
                                                            Tên sản phẩm: {{ $detail->product_name }}<br>
                                                            Đơn giá: {{ number_format($detail->unit_price, 2) }}<br>
                                                            Số lượng: {{ $detail->quantity }}<br>
                                                            Thành tiền: {{ number_format($detail->total_price, 2) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                @if(!$loop->last)
                                                    <hr>
                                                @endif
                                            @endforeach

                                            <!-- Các hành động tùy theo trạng thái đơn hàng -->
                                            {{-- @if($order->orderStatus->name === 'pending')
                                                <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger">Hủy đơn hàng</button>
                                                </form>
                                            @elseif($order->orderStatus->name === 'cancelled')
                                                <form action="{{ route('client.orders.reorder', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có muốn mua lại đơn hàng này?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary">Mua lại</button>
                                                </form>
                                            @else
                                                <p>Đơn hàng này không thể hủy.</p>
                                            @endif --}}
                                        </div>
                                    </div>
                                @endforeach

                                </div>

                            </div>

                            <!-- Các tab khác giữ nguyên... -->
                            <div class="tab-pane fade" id="downloads">
                                <h3>Downloads</h3>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Downloads</th>
                                                <th>Expires</th>
                                                <th>Download</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Shopnovilla - Free Real Estate PSD Template</td>
                                                <td>May 10, 2018</td>
                                                <td><span class="danger">Expired</span></td>
                                                <td><a href="#" class="view">Click Here To Download Your File</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Organic - ecommerce html template</td>
                                                <td>Sep 11, 2018</td>
                                                <td>Never</td>
                                                <td><a href="#" class="view">Click Here To Download Your File</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="address">
                                <p>The following addresses will be used on the checkout page by default.</p>
                                <h4 class="billing-address">Billing address</h4>
                                <a href="#" class="view">Edit</a>
                                <p><strong>Bobby Jackson</strong></p>
                                <address>
                                    House #15<br>
                                    Road #1<br>
                                    Block #C <br>
                                    Banasree <br>
                                    Dhaka <br>
                                    1212
                                </address>
                                <p>Bangladesh</p>
                            </div>

                            <div class="tab-pane fade" id="account-details">
                                <h3>Account details</h3>
                                <div class="login">
                                    <div class="login_form_container">
                                        <div class="account_login_form">
                                            <form action="#">
                                                <p>Already have an account? <a href="#">Log in instead!</a></p>
                                                <div class="input-radio">
                                                    <span class="custom-radio"><input type="radio" value="1"
                                                            name="id_gender"> Mr.</span>
                                                    <span class="custom-radio"><input type="radio" value="1"
                                                            name="id_gender"> Mrs.</span>
                                                </div>
                                                <br>
                                                <label>First Name</label>
                                                <input type="text" name="first-name">
                                                <label>Last Name</label>
                                                <input type="text" name="last-name">
                                                <label>Email</label>
                                                <input type="text" name="email-name">
                                                <label>Password</label>
                                                <input type="password" name="user-password">
                                                <label>Birthdate</label>
                                                <input type="text" placeholder="MM/DD/YYYY" name="birthday">
                                                <span class="example">(E.g.: 05/31/1970)</span>
                                                <br>
                                                <span class="custom_checkbox">
                                                    <input type="checkbox" value="1" name="optin">
                                                    <label>Receive offers from our partners</label>
                                                </span>
                                                <br>
                                                <span class="custom_checkbox">
                                                    <input type="checkbox" value="1" name="newsletter">
                                                    <label>Sign up for our newsletter<br><em>You may unsubscribe at any
                                                            moment. For that purpose, please find our contact info in the
                                                            legal notice.</em></label>
                                                </span>
                                                <div class="save_button primary_btn default_button">
                                                    <button type="submit">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div> <!-- End Main Content Area -->
                </div>
            </div>
        </div>
    </section>
@endsection
