$(document).ready(function () {
    document.querySelectorAll(".value-select").forEach(function (select) {
        select.addEventListener("change", updatePrice);
    });
});
function addToCart() {}
function updatePrice() {
    var selectedAttributes = {};
    var selects = document.querySelectorAll(".value-select");

    selects.forEach(function (select) {
        var attributeName = select.previousElementSibling.textContent.trim(); // Lấy tên thuộc tính
        var selectedValue = select.value;

        if (selectedValue) {
            // Lấy giá trị thực tế thay vì ID
            var valueText = select.options[select.selectedIndex].text;
            selectedAttributes[attributeName] = valueText;
            console.log(selectedAttributes); // In ra đối tượng
        }
    });

    var variant = product.variants.find(function (variant) {
        return variant.attribute_values.every(function (attribute) {
            return (
                selectedAttributes[attribute.attribute_name] === attribute.value
            );
        });
    });
    var quantityInput = document.querySelector(".quantity");

    if (variant) {
        document.querySelector(".current_price").textContent =
            variant.price + " VND";
        document.querySelector(".stock").textContent = variant.quantity;

        quantityInput.max = variant.quantity;
        quantityInput.min = 1;

        // console.log(variant.price);
    } else {
        // console.log("Không tìm thấy phiên bản phù hợp.");
        document.querySelector(".current_price").textContent =
            product.min_price + " - " + product.max_price + " VND"; // Giá mặc định
        document.querySelector(".stock").textContent = product.quantity;
        quantityInput.max = product.quantity;
        quantityInput.min = 1;
    }
}
