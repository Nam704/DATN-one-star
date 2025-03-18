import Echo from "laravel-echo";
import "./bootstrap";

$(document).ready(function () {
    console.log("Client script loaded");
    var csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    getCart();
    $(document).on("click", ".delete_item", function (event) {
        event.preventDefault();
        var id = $(this).data("id");
        removeFromCart(id, csrfToken);
    });
    // Lắng nghe sự kiện trên kênh private
    window.Echo.private(`notifications.${user.id}`).listen(
        "OrderNotification",
        (event) => {
            Toastify({
                text: `New notification: ${event.message}`,
                duration: 3000, // Hiển thị trong 3 giây
                gravity: "top", // Vị trí: trên cùng
                position: "right", // Vị trí: bên phải
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                stopOnFocus: true, // Dừng khi hover vào popup
                close: true, // Tự động đóng sau khi hiển thị
            }).showToast();
        }
    );
});
function removeFromCart(id_variant, token) {
    $.ajax({
        url: "http://127.0.0.1:8000/client/carts/remove",
        method: "POST",
        data: {
            _token: token,
            id_variant: id_variant,
        },
        success: function (response) {
            // alert(response.message);
            getCart();
        },
        error: function (xhr) {
            alert("Error removing product from cart.");
            console.error(xhr.responseText);
        },
    });
}
function getCart() {
    $.ajax({
        url: "http://127.0.0.1:8000/client/carts/get",
        method: "GET",
        success: function (response) {
            console.log("Cart data from getCart:", response);
            updateCartUI(response);
        },
        error: function (xhr) {
            console.error("Error getting cart:", xhr.responseText);
        },
    });
}
function updateCartUI(cartItems) {
    var cartContainer = $(".cart_items");
    cartContainer.empty();
    // console.log("Cart items in updateCartUI:", cartItems);

    if (Object.keys(cartItems).length === 0) {
        cartContainer.append("<p class='text-center'>Your cart is empty.</p>");
    } else {
        $.each(cartItems, function (index, item) {
            if (!item.image) {
                console.warn("Missing variant or images for item:", item.sku);
                return; // Bỏ qua item lỗi
            }

            var totalPrice = item.quantity * item.price;
            var formattedTotalPrice =
                parseFloat(totalPrice).toLocaleString("vi-VN");

            var cartItem = `
                <div class="cart_item">
                    <div class="cart_img">
                        <a href="#"><img src="${item.image}" alt=""></a>
                    </div>
                    <div class="cart_info">
                        <a href="#">${item.name}</a>
                        <span class="sku">SKU: ${item.sku}</span>
                        <span class="quantity">Qty: ${item.quantity}</span>
                        <span class="price_cart">${formattedTotalPrice} VND</span>
                       
                    </div>
                    <div class="cart_remove">
                        <a href="#" class="delete_item" data-id="${item.id_variant}">
                        <i class="ion-android-close"></i>
                        </a>
                    </div>
                </div>
            `;
            cartContainer.append(cartItem);
        });
    }
}
