<div class="Checkout_section mt-32">
    <div class="container">
        <div class="row">
            <div class="col-3">
                <button class="btn btn-warning" id="back_btn">Quay lại</button>
            </div>
        </div>
        {{-- @include('client.checkout.have-account') --}}
        <div class="checkout_form">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <form action="#">
                        <h3>Chi tiết thanh toán</h3>
                        <div class="row">

                            <div class="col-lg-12 mb-20">
                                <label> Tên người dùng <span>*</span></label>
                                <input id="name_user" type="text">
                            </div>
                            <div class="col-lg-6 mb-20">
                                <label>Số điện thoại<span>*</span></label>
                                <input type="text">

                            </div>
                            <div class="col-lg-6 mb-20">
                                <label> Email  <span>*</span></label>
                                <input type="text">

                            </div>



                            <div class="col-12 mb-20">
                                <label>Địa chỉ chi tiết <span>*</span></label>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <select name="province" class="form-select" id="province">
                                            <option value="" {{ old('province') ? 'selected' : '' }}>
                                                Chọn Tỉnh/thành phố
                                            </option>
                                        </select>
                                        @error('province')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <select name="district" class="form-select" id="district">
                                            <option value="" {{ old('district') ? 'selected' : '' }}>
                                                Chọn Quận/huyện
                                            </option>
                                        </select>
                                        @error('district')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <select name="ward" class="form-select" id="ward">
                                            <option value="" {{ old('ward')? 'selected' : '' }}>Chọn
                                                Phường/xã
                                            </option>
                                        </select>
                                        @error('ward')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>


                                </div>
                                <input placeholder="Số nhà và tên đường" type="text">
                            </div>



                            {{-- @include('client.checkout.different-address') --}}
                            <div class="col-12">
                                <div class="order-notes">
                                    <label for="order_note">Ghi chú đơn hàng</label>
                                    <textarea id="order_note"
                                        placeholder="Ghi chú về đơn hàng của bạn"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6 col-md-6">
                    <form action="#">
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
                                    <tr>
                                        <td> Handbag fringilla <strong> × 2</strong></td>
                                        <td> $165.00</td>
                                    </tr>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Cart Subtotal</th>
                                        <td>$215.00</td>
                                    </tr>
                                    <tr>
                                        <th>Shipping</th>
                                        <td><strong>$5.00</strong></td>
                                    </tr>
                                    <tr class="order_total">
                                        <th>Order Total</th>
                                        <td><strong>$220.00</strong></td>
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
                                <input id="payment_defult" name="check_method" type="radio"
                                    data-target="createp_account" />
                                <label for="payment_defult" data-bs-toggle="collapse" href="#collapsedefult"
                                    aria-controls="collapsedefult">PayPal <img src="assets/img/icon/papyel.png"
                                        alt=""></label>

                                <div id="collapsedefult" class="collapse one" data-parent="#accordion">
                                    <div class="card-body1">
                                        <p>Pay via PayPal; you can pay with your credit card if you don’t have a PayPal
                                            account.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="order_button">
                                <button type="submit">Proceed to PayPal</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
