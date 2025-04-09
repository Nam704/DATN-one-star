// import "../app.js"; // Import để sử dụng GlobalUtils

// $(document).ready(() => {
//     // Lấy các phần tử DOM
//     const $cartTableBody = $("tbody");
//     const $selectAllCheckbox = $("#select_all");
//     const $clearAllBtn = $("#clear_all");
//     const $subtotalElement = $(".subtotal");
//     const $discountElement = $(".discount");
//     const $totalElement = $(".total");
//     const $checkoutBtn = $("#checkout");
//     const $couponCode = $("#coupon_code");

//     let cartData = [];
//     let couponApplied = false;
//     let appliedDiscount = 0;

//     // Lấy giỏ hàng ban đầu
//     GlobalUtils.getCart((cart) => {
//         renderCart(cart);
//     });

//     // Sự kiện áp dụng coupon
//     $("button[type='submit']").on("click", function (e) {
//         e.preventDefault();
//         const couponCode = $couponCode.val().trim();
//         const selectedItems = getSelectedItems();
//         const variantIds = selectedItems.map((item) => item.id_variant);
//         const subtotal = selectedItems.reduce(
//             (sum, item) => sum + parseFloat(item.price) * item.quantity,
//             0
//         );

//         if (!couponCode) {
//             GlobalUtils.showNotification("Vui lòng nhập mã coupon!", {
//                 backgroundColor: "#ff4444",
//             });
//             return;
//         }

//         if (variantIds.length === 0) {
//             GlobalUtils.showNotification(
//                 "Vui lòng chọn ít nhất một sản phẩm!",
//                 {
//                     backgroundColor: "#ff4444",
//                 }
//             );
//             return;
//         }

//         // Gọi API để kiểm tra coupon
//         axios
//             .post(`${GlobalUtils.baseUrl}/api/coupon/apply`, {
//                 coupon: couponCode,
//                 variants: variantIds,
//                 subtotal: subtotal,
//             })
//             .then((response) => {
//                 if (response.data.success) {
//                     couponApplied = true;
//                     appliedDiscount = response.data.discount;
//                     updateCartTotals(selectedItems, appliedDiscount);
//                     GlobalUtils.showNotification("Áp dụng coupon thành công!");
//                 } else {
//                     couponApplied = false;
//                     appliedDiscount = 0;
//                     updateCartTotals(selectedItems, 0);
//                     GlobalUtils.showNotification(response.data.message, {
//                         backgroundColor: "#ff4444",
//                     });
//                 }
//             })
//             .catch((error) => {
//                 console.error("Error applying coupon:", error);
//                 couponApplied = false;
//                 appliedDiscount = 0;
//                 updateCartTotals(selectedItems, 0);
//                 GlobalUtils.showNotification("Không thể áp dụng coupon", {
//                     backgroundColor: "#ff4444",
//                 });
//             });
//     });

//     // Hàm hiển thị giỏ hàng
//     function renderCart(cart) {
//         cartData = cart;
//         $cartTableBody.empty();

//         if (!cart || cart.length === 0) {
//             $cartTableBody.append(
//                 "<tr><td colspan='7' class='text-center'>Giỏ hàng trống</td></tr>"
//             );
//             updateCartTotals([]);
//             return;
//         }

//         cart.forEach((item) => {
//             const totalPrice = parseFloat(item.price) * item.quantity;
//             const formattedPrice = GlobalUtils.formatPrice(item.price);
//             const formattedTotal = GlobalUtils.formatPrice(totalPrice);

//             const cartRow = `
//                 <tr data-id="${item.id_variant}">
//                     <td>
//                         <input type="checkbox" class="product_checkbox" value="${item.id_variant}">
//                     </td>
//                     <td class="product_name">
//                         <a href="#">${item.name}</a>
//                         <div class="sku">SKU: ${item.sku}</div>
//                     </td>
//                     <td class="product_thumb">
//                         <a href="#">
//                             <img style="width: 50px" src="${item.image}" alt="${item.name}">
//                         </a>
//                     </td>
//                     <td class="product-price">${formattedPrice}</td>
//                     <td class="product_quantity">
//                         <label>Quantity</label>
//                         <input min="1" max="10" value="${item.quantity}" type="number" class="quantity-input">
//                     </td>
//                     <td class="product_total">${formattedTotal}</td>
//                     <td class="product_remove">
//                         <a href="#" class="remove-item" data-id="${item.id_variant}">
//                             <i class="fa fa-trash-o"></i>
//                         </a>
//                     </td>
//                 </tr>
//             `;
//             $cartTableBody.append(cartRow);
//         });

//         updateCartTotals(getSelectedItems());
//     }

//     // Hàm lấy các sản phẩm được chọn
//     function getSelectedItems() {
//         const selectedIds = $(".product_checkbox:checked")
//             .map(function () {
//                 return $(this).val();
//             })
//             .get();

//         return cartData.filter((item) =>
//             selectedIds.includes(String(item.id_variant))
//         );
//     }

//     // Hàm cập nhật tổng tiền
//     function updateCartTotals(selectedItems, discount = 0) {
//         const subtotal = selectedItems.reduce(
//             (sum, item) => sum + parseFloat(item.price) * item.quantity,
//             0
//         );
//         const total = subtotal - discount;

//         $subtotalElement.text(GlobalUtils.formatPrice(subtotal));
//         $discountElement.text(GlobalUtils.formatPrice(discount));
//         $totalElement.text(GlobalUtils.formatPrice(total));
//     }

//     // Sự kiện chọn tất cả
//     $selectAllCheckbox.on("change", function () {
//         $(".product_checkbox").prop("checked", this.checked);
//         updateCartTotals(getSelectedItems(), appliedDiscount);
//     });

//     // Sự kiện chọn từng sản phẩm
//     $cartTableBody.on("change", ".product_checkbox", function () {
//         const allChecked =
//             $(".product_checkbox").length ===
//             $(".product_checkbox:checked").length;
//         $selectAllCheckbox.prop("checked", allChecked);
//         updateCartTotals(getSelectedItems(), appliedDiscount);
//     });

//     // Sự kiện thay đổi số lượng
//     $cartTableBody.on("change", ".quantity-input", function () {
//         const $row = $(this).closest("tr");
//         const variantId = $row.data("id");
//         const newQuantity = parseInt($(this).val());

//         if (newQuantity < 1 || newQuantity > 10) {
//             GlobalUtils.showNotification("Số lượng phải từ 1 đến 10!", {
//                 backgroundColor: "#ff4444",
//             });
//             $(this).val(1);
//             return;
//         }

//         GlobalUtils.updateCart(variantId, newQuantity, (cart) => {
//             renderCart(cart);
//             GlobalUtils.updateCartUI(cart);
//             couponApplied = false; // Reset coupon khi thay đổi giỏ hàng
//             appliedDiscount = 0;
//             updateCartTotals(getSelectedItems(), 0);
//         });
//     });

//     // Sự kiện xóa sản phẩm
//     $cartTableBody.on("click", ".remove-item", function (e) {
//         e.preventDefault();
//         const variantId = $(this).data("id");
//         GlobalUtils.removeFromCart(variantId, (cart) => {
//             renderCart(cart);
//             GlobalUtils.updateCartUI(cart);
//             couponApplied = false; // Reset coupon khi thay đổi giỏ hàng
//             appliedDiscount = 0;
//             updateCartTotals(getSelectedItems(), 0);
//         });
//     });

//     // Sự kiện xóa toàn bộ giỏ hàng
//     $clearAllBtn.on("click", (e) => {
//         e.preventDefault();
//         GlobalUtils.clearCart((cart) => {
//             renderCart(cart);
//             couponApplied = false;
//             appliedDiscount = 0;
//             updateCartTotals([], 0);
//         });
//     });

//     // Sự kiện checkout
//     $checkoutBtn.on("click", (e) => {
//         e.preventDefault();
//         const selectedItems = getSelectedItems();
//         const couponCode = $couponCode.val().trim();

//         if (selectedItems.length === 0) {
//             GlobalUtils.showNotification(
//                 "Vui lòng chọn ít nhất một sản phẩm để thanh toán!",
//                 { backgroundColor: "#ff4444" }
//             );
//             return;
//         }

//         // Kiểm tra coupon nếu có
//         if (couponCode && !couponApplied) {
//             GlobalUtils.showNotification(
//                 "Coupon không hợp lệ, vui lòng kiểm tra lại!",
//                 { backgroundColor: "#ff4444" }
//             );
//             return;
//         }

//         const data = {
//             variants: selectedItems,
//             coupon: couponCode || null,
//             discount: appliedDiscount,
//         };

//         axios
//             .post(`${GlobalUtils.baseUrl}/client/checkout`, data)
//             .then((response) => {
//                 if (response.data.success) {
//                     window.location.href = response.data.redirectUrl;
//                 } else {
//                     GlobalUtils.showNotification(response.data.message, {
//                         backgroundColor: "#ff4444",
//                     });
//                 }
//             })
//             .catch((error) => {
//                 console.error("Checkout error:", error);
//                 GlobalUtils.showNotification("Không thể tiến hành thanh toán", {
//                     backgroundColor: "#ff4444",
//                 });
//             });

//         GlobalUtils.showNotification("Chuyển hướng đến trang thanh toán...");
//     });
// });
import "../app.js"; // Import để sử dụng GlobalUtils

$(document).ready(() => {
    // Lấy các phần tử DOM
    const $cartTableBody = $("tbody");
    const $selectAllCheckbox = $("#select_all");
    const $clearAllBtn = $("#clear_all");
    const $subtotalElement = $(".subtotal");
    const $discountElement = $(".discount");
    const $totalElement = $(".total");
    const $checkoutBtn = $("#checkout");
    const $couponCode = $("#coupon_code");

    let cartData = [];
    let couponApplied = false;
    let appliedDiscount = 0;

    // Lấy giỏ hàng ban đầu
    GlobalUtils.getCart((cart) => {
        renderCart(cart);
    });

    // Sự kiện áp dụng coupon
    $("button[type='submit']").on("click", function (e) {
        e.preventDefault();
        const couponCode = $couponCode.val().trim();
        const selectedItems = getSelectedItems();
        const variantIds = selectedItems.map((item) => item.id_variant);
        const subtotal = selectedItems.reduce(
            (sum, item) => sum + parseFloat(item.price) * item.quantity,
            0
        );

        if (!couponCode) {
            GlobalUtils.showNotification("Vui lòng nhập mã coupon!", {
                backgroundColor: "#ff4444",
            });
            return;
        }

        if (variantIds.length === 0) {
            GlobalUtils.showNotification(
                "Vui lòng chọn ít nhất một sản phẩm!",
                {
                    backgroundColor: "#ff4444",
                }
            );
            return;
        }

        // Gọi API để kiểm tra coupon
        axios
            .post(`${GlobalUtils.baseUrl}/api/coupon/apply`, {
                coupon: couponCode,
                variants: variantIds,
                subtotal: subtotal,
            })
            .then((response) => {
                if (response.data.success) {
                    couponApplied = true;
                    appliedDiscount = response.data.discount;
                    updateCartTotals(selectedItems, appliedDiscount);
                    GlobalUtils.showNotification("Áp dụng coupon thành công!");
                } else {
                    couponApplied = false;
                    appliedDiscount = 0;
                    updateCartTotals(selectedItems, 0);
                    GlobalUtils.showNotification(response.data.message, {
                        backgroundColor: "#ff4444",
                    });
                }
            })
            .catch((error) => {
                console.error("Error applying coupon:", error);
                couponApplied = false;
                appliedDiscount = 0;
                updateCartTotals(selectedItems, 0);
                GlobalUtils.showNotification("Không thể áp dụng coupon", {
                    backgroundColor: "#ff4444",
                });
            });
    });

    // Hàm hiển thị giỏ hàng
    function renderCart(cart) {
        cartData = cart;
        $cartTableBody.empty();

        if (!cart || cart.length === 0) {
            $cartTableBody.append(
                "<tr><td colspan='7' class='text-center'>Giỏ hàng trống</td></tr>"
            );
            updateCartTotals([]);
            return;
        }

        cart.forEach((item) => {
            const totalPrice = parseFloat(item.price) * item.quantity;
            const formattedPrice = GlobalUtils.formatPrice(item.price);
            const formattedTotal = GlobalUtils.formatPrice(totalPrice);

            // Tạo chuỗi thuộc tính để hiển thị
            const attributes = item.values
                .map((attr) => `${attr.attribute_name}: ${attr.value}`)
                .join(", ");

            const cartRow = `
                <tr data-id="${item.id_variant}">
                    <td>
                        <input type="checkbox" class="product_checkbox" value="${item.id_variant}">
                    </td>
                    <td class="product_name">
                        <a href="#">${item.name}</a>
                        <div class="sku">SKU: ${item.sku}</div>
                        <div class="attributes">${attributes}</div>
                    </td>
                    <td class="product_thumb">
                        <a href="#">
                            <img style="width: 50px" src="${item.image}" alt="${item.name}">
                        </a>
                    </td>
                    <td class="product-price">${formattedPrice}</td>
                    <td class="product_quantity">
                        <label>Quantity</label>
                        <input min="1" max="10" value="${item.quantity}" type="number" class="quantity-input">
                    </td>
                    <td class="product_total">${formattedTotal}</td>
                    <td class="product_remove">
                        <a href="#" class="remove-item" data-id="${item.id_variant}">
                            <i class="fa fa-trash-o"></i>
                        </a>
                    </td>
                </tr>
            `;
            $cartTableBody.append(cartRow);
        });

        updateCartTotals(getSelectedItems());
    }

    // Hàm lấy các sản phẩm được chọn
    function getSelectedItems() {
        const selectedIds = $(".product_checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        return cartData.filter((item) =>
            selectedIds.includes(String(item.id_variant))
        );
    }

    // Hàm cập nhật tổng tiền
    function updateCartTotals(selectedItems, discount = 0) {
        const subtotal = selectedItems.reduce(
            (sum, item) => sum + parseFloat(item.price) * item.quantity,
            0
        );
        const total = subtotal - discount;

        $subtotalElement.text(GlobalUtils.formatPrice(subtotal));
        $discountElement.text(GlobalUtils.formatPrice(discount));
        $totalElement.text(GlobalUtils.formatPrice(total));
    }

    // Sự kiện chọn tất cả
    $selectAllCheckbox.on("change", function () {
        $(".product_checkbox").prop("checked", this.checked);
        updateCartTotals(getSelectedItems(), appliedDiscount);
    });

    // Sự kiện chọn từng sản phẩm
    $cartTableBody.on("change", ".product_checkbox", function () {
        const allChecked =
            $(".product_checkbox").length ===
            $(".product_checkbox:checked").length;
        $selectAllCheckbox.prop("checked", allChecked);
        updateCartTotals(getSelectedItems(), appliedDiscount);
    });

    // Sự kiện thay đổi số lượng
    $cartTableBody.on("change", ".quantity-input", function () {
        const $row = $(this).closest("tr");
        const variantId = $row.data("id");
        const newQuantity = parseInt($(this).val());

        if (newQuantity < 1 || newQuantity > 10) {
            GlobalUtils.showNotification("Số lượng phải từ 1 đến 10!", {
                backgroundColor: "#ff4444",
            });
            $(this).val(1);
            return;
        }

        GlobalUtils.updateCart(variantId, newQuantity, (cart) => {
            renderCart(cart);
            GlobalUtils.updateCartUI(cart);
            couponApplied = false; // Reset coupon khi thay đổi giỏ hàng
            appliedDiscount = 0;
            updateCartTotals(getSelectedItems(), 0);
        });
    });

    // Sự kiện xóa sản phẩm
    $cartTableBody.on("click", ".remove-item", function (e) {
        e.preventDefault();
        const variantId = $(this).data("id");
        GlobalUtils.removeFromCart(variantId, (cart) => {
            renderCart(cart);
            GlobalUtils.updateCartUI(cart);
            couponApplied = false; // Reset coupon khi thay đổi giỏ hàng
            appliedDiscount = 0;
            updateCartTotals(getSelectedItems(), 0);
        });
    });

    // Sự kiện xóa toàn bộ giỏ hàng
    $clearAllBtn.on("click", (e) => {
        e.preventDefault();
        GlobalUtils.clearCart((cart) => {
            renderCart(cart);
            couponApplied = false;
            appliedDiscount = 0;
            updateCartTotals([], 0);
        });
    });

    // Sự kiện checkout
    $checkoutBtn.on("click", (e) => {
        e.preventDefault();
        const selectedItems = getSelectedItems();
        const couponCode = $couponCode.val().trim();

        if (selectedItems.length === 0) {
            GlobalUtils.showNotification(
                "Vui lòng chọn ít nhất một sản phẩm để thanh toán!",
                { backgroundColor: "#ff4444" }
            );
            return;
        }

        // Kiểm tra coupon nếu có
        if (couponCode && !couponApplied) {
            GlobalUtils.showNotification(
                "Coupon không hợp lệ, vui lòng kiểm tra lại!",
                { backgroundColor: "#ff4444" }
            );
            return;
        }

        const data = {
            variants: selectedItems,
            coupon: couponCode || null,
            discount: appliedDiscount,
        };

        axios
            .post(`${GlobalUtils.baseUrl}/client/checkout`, data)
            .then((response) => {
                if (response.data.success) {
                    window.location.href = response.data.redirectUrl;
                } else {
                    GlobalUtils.showNotification(response.data.message, {
                        backgroundColor: "#ff4444",
                    });
                }
            })
            .catch((error) => {
                console.error("Checkout error:", error);
                GlobalUtils.showNotification("Không thể tiến hành thanh toán", {
                    backgroundColor: "#ff4444",
                });
            });

        GlobalUtils.showNotification("Chuyển hướng đến trang thanh toán...");
    });
});
