@extends('client.layouts.home.layout')
@section('title', 'Cart')

@section('content')
    <div class="content">
        <div class="shopping_cart_area mt-32">
            <div class="container">
                <form action="#">
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
                                            @foreach ($cart as $item)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="product_checkbox"
                                                            value="{{ $item->id_variant }}">
                                                    </td>
                                                    <td class="product_name">

                                                        <a href="#">{{ $item->name }}</a>
                                                        <div class="sku">sku: {{ $item->sku }}</div>
                                                    </td>
                                                    <td class="product_thumb">
                                                        <a href="#">
                                                            <img style="width: 50px" src="{{ $item->image }}"
                                                                alt="">
                                                        </a>
                                                    </td>

                                                    <td class="product-price">{{ $item->price }}</td>
                                                    <td class="product_quantity">
                                                        <label>Quantity</label>
                                                        <input min="1" max="10" value="{{ $item->quantity }}"
                                                            type="number">
                                                    </td>
                                                    <td class="product_total"></td>
                                                    <td class="product_remove"><a href="#"
                                                            data-id="{{ $item->id_variant }}"><i
                                                                class="fa fa-trash-o"></i></a></td>

                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                                <div class="cart_clear">
                                    <button id="clear_all" class="btn btn-danger">clear cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--coupon code area start-->
                    <div class="coupon_area">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="coupon_code left">
                                    <h3>Coupon</h3>
                                    <div class="coupon_inner">
                                        <p>Enter your coupon code if you have one.</p>
                                        <input placeholder="Coupon code" type="text">
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
                                        <div class="cart_subtotal ">
                                            <p>Shipping</p>
                                            <p class="cart_amount shipping">
                                                25000
                                            </p>
                                        </div>
                                        <a href="#">Calculate shipping</a>

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
                    <!--coupon code area end-->
                </form>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('client/api/cart.js') }}"></script>
@endsection
