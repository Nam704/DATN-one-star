$(document).ready(function () {
    console.log(user);
    var csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    getCart();
    // Lắng nghe sự kiện thay đổi của các select (cập nhật giá trị khi thay đổi biến thể)
    document.querySelectorAll(".value-select").forEach(function (select) {
        select.addEventListener("change", function () {
            updatePrice();
        });
    });

    $(document).on("click", ".delete_item", function (event) {
        event.preventDefault();
        var id = $(this).data("id");
        removeFromCart(id, csrfToken);
    });
    // Lắng nghe sự kiện click vào nút "Add to Cart"
    document
        .getElementById("add-to-cart")
        .addEventListener("click", function (event) {
            event.preventDefault(); // Ngừng hành động mặc định của nút (nếu có)

            // Tìm và lấy biến thể đã chọn
            var variant = getSelectedVariant();

            if (variant) {
                var quantity =
                    document.querySelector(".quantity-to-cart").value; // Lấy số lượng sản phẩm người dùng chọn

                addToCart(variant.id, quantity, csrfToken);
            } else {
                alert("Không có sản phẩm nào hợp lệ được chọn.");
            }
        });
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
function addToCart(id_variant, quantity, token) {
    $.ajax({
        url: "http://127.0.0.1:8000/client/carts/add",
        method: "POST",
        data: {
            _token: token,
            id_variant: id_variant,
            quantity: quantity,
        },
        success: function (response) {
            // alert(response.message);
            // console.log(response.cart);
            // console.dir(response);
            getCart();
        },
        error: function (xhr) {
            alert("Error adding product to cart.");
            console.error(xhr.responseText);
        },
    });
}

// Hàm chung để tìm kiếm biến thể và cập nhật giá, stock
function getSelectedVariant() {
    var selectedAttributes = {};
    var selects = document.querySelectorAll(".value-select");

    selects.forEach(function (select) {
        var attributeName = select.previousElementSibling.textContent.trim(); // Tên thuộc tính
        var selectedValue = select.value;

        if (selectedValue) {
            var valueText = select.options[select.selectedIndex].text; // Giá trị thực tế thay vì ID
            selectedAttributes[attributeName] = valueText;
        }
    });

    // Tìm biến thể phù hợp
    return product.variants.find(function (variant) {
        return variant.attribute_values.every(function (attribute) {
            return (
                selectedAttributes[attribute.attribute_name] === attribute.value
            );
        });
    });
}

// Cập nhật giá và số lượng cho sản phẩm
function updatePrice() {
    var variant = getSelectedVariant(); // Tìm biến thể đã chọn

    var quantityInput = document.querySelector(".quantity-to-cart");

    if (variant) {
        document.querySelector(".current_price").textContent =
            variant.price + " VND";
        document.querySelector(".stock").textContent = variant.quantity;

        // Cập nhật max và min cho input quantity
        quantityInput.max = variant.quantity;
        quantityInput.min = 1;
    } else {
        // Giá mặc định nếu không tìm thấy biến thể phù hợp
        document.querySelector(".current_price").textContent =
            product.min_price + " - " + product.max_price + " VND";
        document.querySelector(".stock").textContent = product.quantity;

        // Cập nhật lại max và min cho input quantity
        quantityInput.max = product.quantity;
        quantityInput.min = 1;
    }
}
