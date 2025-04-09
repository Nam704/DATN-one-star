<div class="mini_cart">
    <div class="cart_close">
        <div class="cart_text">
            <h3>Cart</h3>
        </div>
        <div class="mini_cart_close">
            <a href="javascript:void(0)"><i class="ion-android-close"></i></a>
        </div>
    </div>
    <div class="cart_items">
        <!-- Dữ liệu sẽ được render bởi updateCartUI -->
    </div>

    <div class="mini_cart_table">

        <div class="cart_total mt-10">
            <span>Total:</span>
            <span class="price cart-total"></span>
        </div>
    </div>

    <div class="mini_cart_footer">
        <div class="cart_button">
            <a href="{{ route('client.carts.viewCart') }}">View cart</a>
        </div>

    </div>
</div>
