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

    </div>


    <div class="mini_cart_table">
        <div class="cart_total">
            <span>Sub total:</span>
            <span class="price">${{-- number_format($subtotal,2) --}}</span>
        </div>
        <div class="cart_total mt-10">
            <span>Total:</span>
            <span class="price">${{-- number_format($subtotal,2) --}}</span>
        </div>
    </div>

    <div class="mini_cart_footer">
        <div class="cart_button">
            <a href="{{ route('client.carts.viewCart') }}">View cart</a>
        </div>
        <div class="cart_button">
            <a class="active" href="{{-- route('checkout') --}}">Checkout</a>
        </div>
    </div>

</div>