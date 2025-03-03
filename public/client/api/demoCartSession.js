$(document).ready(function () {
    console.log(user);

    // Lắng nghe sự kiện thay đổi biến thể
    document.querySelectorAll(".value-select").forEach(function (select) {
        select.addEventListener("change", function () {
            updatePrice();
        });
    });

    // Lắng nghe sự kiện thêm vào giỏ hàng
    $("#add-to-cart").on("click", function (event) {
        event.preventDefault();
        var variant = getSelectedVariant();
        var quantity = $(".quantity-to-cart").val();

        if (variant) {
            addToCart(variant.id, quantity);
        } else {
            alert("Please select a valid product variant.");
        }
    });

    // Lấy danh sách giỏ hàng khi load trang
    getCart();

    // Xóa sản phẩm khỏi giỏ hàng
    $(document).on("click", ".remove-cart-item", function () {
        var id_variant = $(this).data("id");
        removeFromCart(id_variant);
    });

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    $(document).on("change", ".cart-quantity", function () {
        var id_variant = $(this).data("id");
        var quantity = $(this).val();
        updateCart(id_variant, quantity);
    });

    // Xóa toàn bộ giỏ hàng
    $("#clear-cart").on("click", function () {
        clearCart();
    });

    // Lưu giỏ hàng vào database (nếu cần)
    $("#save-cart-to-db").on("click", function () {
        saveSessionCartToDatabase();
    });
});

// 🛒 Hàm lấy giỏ hàng
function getCart() {
    $.ajax({
        url: "http://127.0.0.1:8000/client/carts/get",
        method: "GET",
        success: function (response) {
            console.log("Cart data:", response);
            updateCartUI(response);
        },
        error: function (xhr) {
            console.error("Error getting cart:", xhr.responseText);
        },
    });
}

// 🛒 Hàm thêm sản phẩm vào giỏ hàng
function addToCart(id_variant, quantity) {
    $.ajax({
        url: "http://127.0.0.1:8000/client/carts/add",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id_variant: id_variant,
            quantity: quantity,
        },
        success: function (response) {
            alert(response.message);
            getCart();
        },
        error: function (xhr) {
            alert("Error adding product to cart.");
            console.error(xhr.responseText);
        },
    });
}

// 🛒 Hàm cập nhật số lượng sản phẩm trong giỏ hàng
function updateCart(id_variant, quantity) {
    $.ajax({
        url: "http://127.0.0.1:8000/client/carts/update",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id_variant: id_variant,
            quantity: quantity,
        },
        success: function (response) {
            alert(response.message);
            getCart();
        },
        error: function (xhr) {
            alert("Error updating cart.");
            console.error(xhr.responseText);
        },
    });
}

// 🛒 Hàm xóa sản phẩm khỏi giỏ hàng
function removeFromCart(id_variant) {
    $.ajax({
        url: "http://127.0.0.1:8000/client/carts/remove",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id_variant: id_variant,
        },
        success: function (response) {
            alert(response.message);
            getCart();
        },
        error: function (xhr) {
            alert("Error removing product from cart.");
            console.error(xhr.responseText);
        },
    });
}

// 🛒 Hàm xóa toàn bộ giỏ hàng
function clearCart() {
    $.ajax({
        url: "http://127.0.0.1:8000/client/carts/clear",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            alert(response.message);
            getCart();
        },
        error: function (xhr) {
            alert("Error clearing cart.");
            console.error(xhr.responseText);
        },
    });
}

// 🛒 Hàm lưu giỏ hàng vào database
function saveSessionCartToDatabase() {
    $.ajax({
        url: "http://127.0.0.1:8000/client/carts/save-to-db",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            alert(response.message);
        },
        error: function (xhr) {
            alert("Error saving cart to database.");
            console.error(xhr.responseText);
        },
    });
}

// 🛒 Cập nhật giao diện giỏ hàng
function updateCartUI(cartItems) {
    var cartContainer = $(".mini_cart");
    cartContainer.empty();

    if (Object.keys(cartItems).length === 0) {
        cartContainer.append("<p class='text-center'>Your cart is empty.</p>");
    } else {
        $.each(cartItems, function (id_variant, item) {
            var cartItem = `
                <div class="cart_item">
                    <div class="cart_img">
                        <a href="#"><img src="${item.image}" alt=""></a>
                    </div>
                    <div class="cart_info">
                        <a href="#">${item.name}</a>
                        <span class="quantity">Qty: <input type="number" class="cart-quantity" data-id="${id_variant}" value="${item.quantity}" min="1"></span>
                        <span class="price_cart">${item.price} VND</span>
                    </div>
                    <div class="cart_remove">
                        <button class="remove-cart-item" data-id="${id_variant}"><i class="ion-android-close"></i></button>
                    </div>
                </div>
            `;
            cartContainer.append(cartItem);
        });
    }
}
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
