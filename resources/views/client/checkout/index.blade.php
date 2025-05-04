@extends('client.layouts.home.layout')
@section('title', 'Thanh toán')
@section('content')
    <div class="Checkout_section mt-32">
        <div class="container">
            <div class="checkout_form">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="billing_details">
                            <h3>Chi tiết thanh toán</h3>
                            <div class="row">
                                <div class="col-lg-12 mb-20">
                                    <label> Tên người dùng <span>*</span></label>
                                    <input id="name_user" name="name" value="{{ $user->name }}" type="text">
                                </div>
                                <div class="col-lg-6 mb-20">
                                    <label>Số điện thoại<span>*</span></label>
                                    <input id="phone" name="phone" type="text" value="{{ $user->phone ?? '' }}">
                                </div>
                                <div class="col-lg-6 mb-20">
                                    <label> Email <span>*</span></label>
                                    <input id="email" name="email" type="text" value="{{ $user->email }}">
                                </div>
                                <div class="col-12 mb-20" id="address" data-id="{{ $id_address }}">
                                    <label>Tên đường, số nhà...<span>*</span></label>
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <select name="province" class="form-select" id="province"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="district" class="form-select" id="district"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="ward" class="form-select" id="ward"></select>
                                        </div>
                                    </div>
                                    <input placeholder="Số nhà, tên đường..." type="text" name="address_detail"
                                        id="address_detail" value="">
                                </div>
                                <div class="col-12">
                                    <div class="order-notes">
                                        <label for="order_note">Ghi chú</label>
                                        <textarea id="order_note" name="order_note" placeholder="Ghi chú đơn hàng của bạn."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="coupon_detail mt-3">
                            <h3>Chi tiết mã giảm giá</h3>
                            <div class="card mb-4">
                                @if (!empty($data['coupon']))
                                    <div class="card-body">
                                        <p><strong>Mã giảm giá áp dụng:</strong> {{ $data['coupon'] }}</p>
                                        <p><strong>Giảm giá:</strong> {{ number_format($discount, 0, ',', '.') }} ₫</p>
                                    </div>
                                @else
                                    <div class="card-body">
                                        <p>Không áp dụng mã giảm giá.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">

                        <h3>Đơn hàng</h3>
                        <div class="order_table table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Tổng tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['variants'] as $item)
                                        <tr>
                                            <td class="row">
                                                <div class="col-4">
                                                    <img src="{{ asset($item['image']) }}" alt="" width="50px">
                                                </div>
                                                <div class="col-8">
                                                    <div>{{ $item['name'] }}</div>
                                                    <div>{{ $item['sku'] }}</div>
                                                    <div>
                                                        @foreach ($item['values'] as $value)
                                                            <span>{{ $value['attribute_name'] }}: {{ $value['value'] }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                    <div>Qty: <strong>{{ $item['quantity'] }}</strong></div>
                                                </div>
                                            </td>
                                            <td>{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} ₫</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Tổng giỏ hàng</th>
                                        <td>{{ number_format($cartSubtotal, 0, ',', '.') }} ₫</td>
                                    </tr>
                                    <tr>
                                        <th>Giảm giá</th>
                                        <td><strong>{{ number_format($discount, 0, ',', '.') }} ₫</strong></td>
                                    </tr>
                                    <tr class="order_total">
                                        <th>Tổng thanh toán</th>
                                        <td><strong>{{ number_format($orderTotal, 0, ',', '.') }} ₫</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="payment_method">
                            <div class="panel-default">
                                <div class="payment_cod">
                                    <input type="radio" id="COD" name="payment_method" value="COD" required />
                                    <label for="COD">COD</label>
                                </div>
                                <div class="payment_vnpay">
                                    <input id="VNPAY" type="radio" name="payment_method" value="VNPAY" required />
                                    <label for="VNPAY">VNPAY
                                        <img src="{{ asset('client/assets/img/icon/papyel.png') }}">
                                    </label>
                                </div>
                                <div class="order_button">
                                    <button type="button" id="process">Thanh toán</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @vite('resources/js/address.js')
    @vite('resources/js/client/checkout.js')

@endsection
