@extends('client.layouts.home.layout')
@section('title', 'Cart')

@section('content')
    <div class="content">
        <div class="shopping_cart_area mt-32">
            <div class="container">

                <div class="row">
                    <div class="col-12">
                        <div class="table_desc">
                            <div class="cart_page table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="product_select">
                                                <input type="checkbox" id="select_all">
                                                Select
                                            </th>
                                            <th class="product_name">Product</th>
                                            <th class="product_thumb">Image</th>
                                            <th class="product-price">Price</th>
                                            <th class="product_quantity">Quantity</th>
                                            <th class="product_total">Total</th>
                                            <th class="product_remove">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dữ liệu sẽ được render bằng JS -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="cart_clear">
                                <button id="clear_all" class="btn btn-danger">Clear Cart</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="coupon_area">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="coupon_code left">
                                <h3>Coupon</h3>
                                <div class="coupon_inner">
                                    <p>Enter your coupon code if you have one.</p>
                                    <input placeholder="Coupon code" id="coupon_code" type="text">
                                    <button type="submit">Apply coupon</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="coupon_code right">
                                <h3>Cart Totals</h3>
                                <div class="coupon_inner">
                                    <div class="cart_subtotal">
                                        <p>Subtotal</p>
                                        <p class="cart_amount subtotal"></p>
                                    </div>
                                    <!-- Thêm hàng Discount -->
                                    <div class="cart_subtotal">
                                        <p>Discount</p>
                                        <p class="cart_amount discount">0 ₫</p>
                                    </div>

                                    <div class="cart_subtotal">
                                        <p>Total</p>
                                        <p class="cart_amount total"></p>
                                    </div>
                                    <div class="checkout_btn">
                                        <a href="#" id="checkout">Proceed to Checkout</a>
                                    </div>
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
    @vite('resources/js/client/cartDetail.js')
@endsection
