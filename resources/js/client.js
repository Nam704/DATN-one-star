import "./app.js";

$(document).ready(function () {
    GlobalUtils.getCart(($cart) => {
        GlobalUtils.updateCartUI($cart);
    });
    // Sự kiện xóa sản phẩm trong mini-cart
    $(".cart_items").on("click", ".delete_item", function (e) {
        e.preventDefault();
        const variantId = $(this).data("id");
        GlobalUtils.removeFromCart(variantId, (cart) => {
            GlobalUtils.updateCartUI(cart);
        });
    });
    // Lắng nghe sự kiện trên kênh private
    window.Echo.private(`notifications.${user.id}`).listen(
        "OrderNotification",
        (event) => {
            GlobalUtils.showNotification(event.message);
        }
    );
});
