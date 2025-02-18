
 <div class="breadcrumbs_area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_content">
                    <h3>Cart</h3>
                    <ul>
                        <li><a href="{{ route('client.home') }}">home</a></li>
                        <li>Shopping Cart</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="shopping_cart_area mt-70">
    <div class="container">
        <form action="#">
            <div class="row">
                <div class="col-12">
                    <div class="table_desc">
                        <div class="cart_page table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="product_thumb">Image</th>
                                        <th class="product_name">Product</th>
                                        <th class="product-price">Price</th>
                                        <th class="product_quantity">Quantity</th>
                                        <th class="product_total">Total</th>
                                        <th class="product_remove">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($cart && $cart->items->count() > 0)
                                        @foreach($cart->items as $item)
                                            <tr>
                                                <td class="product_thumb">
                                                    <img src="{{ Storage::url($item->productVariant->product->image_primary) }}" alt="">
                                                </td>            
                                                <td class="product_name">{{ $item->productVariant->product->name }}</td>
                                                <td class="product-price">{{ number_format($item->price, 2) }}</td>
                                                <td class="product_quantity">
                                                    <div class="quantity-wrapper d-flex align-items-center">
                                                        <button type="button" class="btn btn-sm btn-decrease">-</button>
                                                        <input type="number" min="1" max="100" value="{{ $item->quantity }}" 
                                                            data-item-id="{{ $item->id }}" class="quantity-input" readonly>
                                                        <button type="button" class="btn btn-sm btn-increase">+</button>
                                                    </div>
                                                </td>
                                                <td class="product_total">{{ number_format($item->price * $item->quantity, 2) }}</td>
                                                <td class="product_remove">
                                                    <button type="button" class="btn remove-item" data-id="{{ $item->id }}">
                                                        <i class="fa fa-trash-o"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">Your cart is empty</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    <div class="cart_submit">
                        <button type="button" id="update-cart" class="btn">update cart</button>
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
                                <p>Nhập mã giảm giá nếu có</p>
                                <input id="voucher_code" name="voucher_code" placeholder="Nhập mã giảm giá" type="text">
                                <button type="button" id="apply-coupon" class="button">Áp dụng</button>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="coupon_code right">
                            <h3>Cart Totals</h3>
                            <div class="coupon_inner">
                                <div class="cart_subtotal">
                                    <p>Thành tiền:</p>
                                    <p class="cart_subtotal price">{{ number_format($cart->getTotal()) }}đ</p>
                                </div>
                                <div class="cart_subtotal">
                                    <p>Giảm giá:</p>
                                    <p class="discount_amount">0đ</p>
                                </div>
                                <div class="cart_subtotal">
                                    <p>Tổng tiền:</p>
                                    <p class="final_total price">{{ number_format($cart->getTotal()) }}đ</p>
                                </div>
                                <div class="checkout_btn">
                                    <a href="#">Proceed to Checkout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.quantity-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}
.quantity-input {
    width: 60px;
    text-align: center;
}
.btn-increase, .btn-decrease {
    width: 30px;
    height: 30px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>


@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#apply-coupon').click(function(e) {
        // existing code
    });
});
</script>
@endpush
