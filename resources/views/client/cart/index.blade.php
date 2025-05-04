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
                                            </th>
                                            <th class="product_name">Sản phẩm</th>
                                            <th class="product_thumb">Hình ảnh</th>
                                            <th class="product-price">Thành tiền</th>
                                            <th class="product_quantity">Số lượng</th>
                                            <th class="product_total">Tổng thanh toán</th>
                                            <th class="product_remove">Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dữ liệu sẽ được render bằng JS -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="cart_clear">
                                <button id="clear_all" class="btn btn-danger">Xóa giỏ hàng</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="coupon_area">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="coupon_code left">
                                <h3>Mã giảm giá</h3>
                                <div class="coupon_inner">
                                    <p>Xem các mã giảm giá khả dụng hoặc nhập mã giảm giá.</p>
                                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                                        data-bs-target="#voucherModal">Xem mã giảm giá</button>
                                    <div class="input-group">
                                        <input placeholder="Mã giảm giá" id="coupon_code" type="text" class="form-control">
                                        <button type="submit" class="btn btn-success">Áp dụng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="coupon_code right">
                                <h3>Tổng giỏ hàng</h3>
                                <div class="coupon_inner">
                                    <div class="cart_subtotal">
                                        <p>Tạm tính</p>
                                        <p class="cart_amount subtotal"></p>
                                    </div>
                                    <div class="cart_subtotal">
                                        <p>Giảm giá</p>
                                        <p class="cart_amount discount">0 ₫</p>
                                    </div>
                                    <div class="cart_subtotal">
                                        <p>Tổng cộng</p>
                                        <p class="cart_amount total"></p>
                                    </div>
                                    <div class="checkout_btn">
                                        <a href="#" id="checkout">Tiến hành Thanh toán</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Voucher Modal -->
                <div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="voucherModalLabel">Mã giảm giá khả dụng</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="voucherList" class="row">
                                    <!-- Vouchers will be rendered here via JS -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Modal -->
                <div class="modal fade" id="productsModal" tabindex="-1" aria-labelledby="productsModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productsModalLabel">Sản Phẩm Áp Dụng</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <ul id="productList" class="list-group">
                                    <!-- Products will be rendered here via JS -->
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
    <!-- Bootstrap JS -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
@endsection
