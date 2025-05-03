import "../app.js"; // Import app.js để sử dụng GlobalUtils

$(document).ready(() => {
    // Lấy các phần tử DOM bằng jQuery
    const $attributeSelects = $(".value-select");
    const $priceBox = $("#price-box");
    const $stockElement = $(".stock");
    const $quantityInput = $(".quantity-to-cart");
    const $addToCartBtn = $("#add-to-cart");
    $priceBox.text(
        `${GlobalUtils.formatPrice(
            product.min_price
        )} - ${GlobalUtils.formatPrice(product.max_price)}`
    );
    // Biến để lưu variant hiện tại
    let selectedVariant = null;

    // Hàm tìm variant dựa trên các giá trị thuộc tính được chọn
    function findVariant() {
        const selectedValues = {};
        $attributeSelects.each(function () {
            const attributeName = $(this)
                .attr("name")
                .replace("attribute[", "")
                .replace("]", "");
            const valueId = $(this).val();
            if (valueId) {
                selectedValues[attributeName] = valueId;
            }
        });

        if (Object.keys(selectedValues).length !== $attributeSelects.length) {
            return null;
        }

        return product.variants.find((variant) =>
            variant.attribute_values.every(
                (attr) =>
                    selectedValues[attr.attribute_name] === attr.id.toString()
            )
        );
    }

    // Hàm cập nhật UI (giá, số lượng tồn kho)
    function updateProductInfo(variant) {
        if (variant) {
            selectedVariant = variant;
            $priceBox.text(GlobalUtils.formatPrice(variant.price));
            $stockElement.text(variant.quantity);
            $quantityInput.attr("max", variant.quantity).val(1);
        } else {
            $priceBox.text(
                `${GlobalUtils.formatPrice(
                    product.min_price
                )} - ${GlobalUtils.formatPrice(product.max_price)}`
            );
            $stockElement.text(product.quantity);
            $quantityInput.attr("max", product.quantity);
            selectedVariant = null;
        }
    }

    // Lắng nghe sự kiện thay đổi trên các select
    $attributeSelects.on("change", function () {
        const variant = findVariant();
        updateProductInfo(variant);
    });

    // Xử lý nút "Add to Cart"
    $addToCartBtn.on("click", (e) => {
        e.preventDefault();

        if (!selectedVariant) {
            GlobalUtils.showNotification(
                "Vui lòng chọn đầy đủ các thuộc tính!",
                {
                    backgroundColor: "#ff4444",
                }
            );
            return;
        }

        const quantity = parseInt($quantityInput.val());
        console.log(quantity);
        if (quantity < 1 || quantity > selectedVariant.quantity) {
            GlobalUtils.showNotification("Số lượng không hợp lệ!", {
                backgroundColor: "#ff4444",
            });
            return;
        }

        GlobalUtils.addToCart(selectedVariant.id, quantity, (cart) => {
            // Có thể cập nhật mini-cart hoặc hiển thị thông báo thêm
            GlobalUtils.getCart((updatedCart) => {
                GlobalUtils.updateCartUI(updatedCart);
            });
        });
    });
});
