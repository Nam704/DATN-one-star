@extends('client.layouts.home.layout')
<!--Checkout page section-->
@section('title', 'Thanh toán')
@section('content')
    <div class="Checkout_section mt-32">
        <div class="container">
            {{-- @include('client.checkout.have-account') --}}
            <div class="checkout_form">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <form action="#">
                            <h3>Billing Details</h3>
                            <div class="row">

                                <div class="col-lg-12 mb-20">
                                    <label> Name <span>*</span></label>
                                    <input id="name_user" value="{{ $data['user']->name }}" type="text">
                                </div>
                                <div class="col-lg-6 mb-20">
                                    <label>Phone<span>*</span></label>
                                    <input id="phone" type="text" value="{{ $data['user']->phone ?? '' }}">

                                </div>
                                <div class="col-lg-6 mb-20">
                                    <label> Email Address <span>*</span></label>
                                    <input id="email" type="text" value="{{ $data['user']->email }}">

                                </div>

                                <div class="col-12 mb-20" id="address" data-address="{{ $address->id ?? '' }}">
                                    <label>Street address <span>*</span></label>
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <select name="province" class="form-select" id="province">
                                                <option value="{{ $address->province_id }}"
                                                    {{ old('province', $address->province_id ?? '') ? 'selected' : '' }}>
                                                    {{ $address->province_name }}

                                                </option>
                                            </select>
                                            @error('province')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <select name="district" class="form-select" id="district">
                                                <option value="{{ $address->district_id }}"
                                                    {{ old('district', $address->district_id ?? '') ? 'selected' : '' }}>
                                                    {{ $address->district_name }}

                                                </option>
                                            </select>
                                            @error('district')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <select name="ward" class="form-select" id="ward">
                                                <option value="{{ $address->ward_id }}"
                                                    {{ old('ward', $address->ward_id ?? '') ? 'selected' : '' }}>
                                                    {{ $address->ward_name }}
                                                </option>
                                            </select>
                                            @error('ward')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                    <input placeholder="House number and street name" type="text"
                                        value="{{ $address->address_detail }}" id="address_detail">
                                </div>

                                {{-- @include('client.checkout.different-address') --}}
                                <div class="col-12">
                                    <div class="order-notes">
                                        <label for="order_note">Order Notes</label>
                                        <textarea id="order_note" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <form action="#">
                            <h3>Your order</h3>
                            <div class="order_table table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['details'] as $item)
                                            <tr>
                                                <td class="row">
                                                    <div class="col-4">
                                                        <img src="{{ asset($item['image']) }}" alt=""
                                                            width="50px">
                                                    </div>
                                                    <div class="col-8">
                                                        <div> {{ $item['name'] }}</div>
                                                        <div> {{ $item['sku'] }}</div>
                                                        <div>Qty: <strong> {{ $item['quantity'] }}</strong></div>
                                                    </div>

                                                </td>
                                                <td> {{ $item['price'] }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                        @php
                                            $cart = $data['cart'];
                                        @endphp
                                        <tr>
                                            <th>Cart Subtotal</th>
                                            <td>{{ $cart['subTotal'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shipping</th>
                                            <td><strong>{{ $cart['shipping'] }}</strong></td>
                                        </tr>
                                        <tr class="order_total">
                                            <th>Order Total</th>
                                            <td><strong>{{ $cart['total'] }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="payment_method">
                                {{-- <div class="panel-default">
                                <input id="payment" name="check_method" type="radio" data-target="createp_account" />
                                <label for="payment" data-bs-toggle="collapse" href="#method"
                                    aria-controls="method">Create an account?</label>

                                <div id="method" class="collapse one" data-parent="#accordion">
                                    <div class="card-body1">
                                        <p>Please send a check to Store Name, Store Street, Store Town, Store State /
                                            County, Store Postcode.</p>
                                    </div>
                                </div>
                            </div> --}}
                                <div class="panel-default">
                                    <div class="payment_cod">
                                        <input type="radio" id="COD" name="payment" />
                                        <label for="COD">COD
                                            <img src="">
                                        </label>
                                    </div>
                                    <div class="payment_vnpay">
                                        <input id="VNPAY" type="radio" name="payment" />

                                        <label for="VNPAY">PayPal
                                            <img src="{{ asset('client/assets/img/icon/papyel.png') }}">
                                        </label>

                                    </div>

                                    <div class="order_button">
                                        <button type="submit" id="process">Proceed to PayPal</button>
                                    </div>
                                </div>
                        </form>
                        {{-- demo payment --}}
                        {{-- <form action="{{ route('client.checkout.payment') }}" method="POST">
                            @csrf
                            <button type="submit" name="redirect">VNPAY</button>
                        </form> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Checkout page section end-->
@endsection
@section('scripts')
    @vite('resources/js/address.js')
    <script src="{{ asset('client/api/checkout.js') }}"></script>
@endsection
