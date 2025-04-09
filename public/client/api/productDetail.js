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
