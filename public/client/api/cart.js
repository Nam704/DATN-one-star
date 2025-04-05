// $(function () {
//     const csrfToken = $('meta[name="csrf-token"]').attr("content");

//     $.ajaxSetup({
//         headers: {
//             "X-CSRF-TOKEN": csrfToken,
//         },
//     });

//     // Helper: parse VND to float
//     const parseVND = (str) =>
//         parseFloat(str.replace(/[^\d.-]/g, "").trim()) || 0;

//     // Cập nhật tổng giá cho từng sản phẩm
//     function updateLineTotals() {
//         $(".product_quantity input").each(function () {
//             const quantity = parseInt($(this).val()) || 0;
//             const price = parseVND(
//                 $(this).closest("tr").find(".product-price").text()
//             );
//             const total = price * quantity;

//             $(this)
//                 .closest("tr")
//                 .find(".product_total")
//                 .text(total.toFixed(2) + " VND");
//         });
//         updateCartTotals();
//     }

//     // Cập nhật tổng giỏ hàng
//     function updateCartTotals() {
//         let cartTotal = 0;
//         $("tbody .product_checkbox:checked").each(function () {
//             const productTotal = parseVND(
//                 $(this).closest("tr").find(".product_total").text()
//             );
//             cartTotal += productTotal;
//         });

//         const shipping = parseVND($(".cart_subtotal .shipping").text());
//         const grandTotal = cartTotal + shipping;

//         $(".cart_subtotal .cart_amount")
//             .first()
//             .text(cartTotal.toFixed(2) + " VND");
//         $(".cart_subtotal .cart_amount")
//             .last()
//             .text(grandTotal.toFixed(2) + " VND");
//     }

//     // Gỡ sản phẩm khỏi giỏ
//     function removeCartItem(id_variant) {
//         $.post("/client/carts/remove", { id_variant });
//     }

//     // Gửi dữ liệu khi checkout
//     function bindCheckoutHandler() {
//         $(".checkout_btn").on("click", function (e) {
//             e.preventDefault();
//             const data = buildCartData();

//             $.post("/client/checkout", { data }, function (response) {
//                 window.location.href = response.link;
//             }).fail(function (xhr) {
//                 console.error(xhr.responseText);
//             });
//         });
//     }

//     // Thu thập dữ liệu giỏ hàng
//     function buildCartData() {
//         const cartItems = [];
//         const cart = {};
//         const total = parseVND($(".cart_subtotal .cart_amount").last().text());
//         const shipping = parseVND($(".cart_subtotal .shipping").text());

//         cart.total = total;
//         cart.subTotal = total - shipping;
//         cart.shipping = shipping;
//         cart.id_user = window.user?.id || null;

//         $("tbody tr").each(function () {
//             const checkbox = $(this).find(".product_checkbox");
//             if (checkbox.prop("checked")) {
//                 cartItems.push({
//                     id_variant: checkbox.val(),
//                     quantity: $(this).find(".product_quantity input").val(),
//                     price: parseVND($(this).find(".product-price").text()),
//                     product_total: parseVND(
//                         $(this).find(".product_total").text()
//                     ),
//                     name: $(this).find(".product_name a").text().trim(),
//                     image: $(this).find(".product_thumb img").attr("src"),
//                     sku: $(this).find(".sku").text().replace("sku:", "").trim(),
//                 });
//             }
//         });

//         return { details: cartItems, cart };
//     }

//     // Sự kiện thay đổi số lượng sản phẩm
//     $(".product_quantity input").on("change", updateLineTotals);

//     // Chọn / bỏ chọn tất cả sản phẩm
//     $("#select_all").on("click", function () {
//         $("tbody .product_checkbox").prop("checked", this.checked);
//         updateCartTotals();
//     });

//     // Cập nhật checkbox tổng khi thay đổi từng dòng
//     $("tbody .product_checkbox").on("change", function () {
//         const allChecked =
//             $("tbody .product_checkbox:checked").length ===
//             $("tbody .product_checkbox").length;
//         $("#select_all").prop("checked", allChecked);
//         updateCartTotals();
//     });

//     // Xóa sản phẩm khỏi giỏ
//     $("tbody .product_remove a").on("click", function (e) {
//         e.preventDefault();
//         const row = $(this).closest("tr");
//         const id_variant = row.find(".product_checkbox").val();
//         removeCartItem(id_variant);
//         row.remove();
//         updateLineTotals();
//     });

//     // Khởi động ban đầu
//     updateLineTotals();
//     bindCheckoutHandler();
// });
$(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
    });

    const parseVND = (str) =>
        parseFloat(str.replace(/[^\d.-]/g, "").trim()) || 0;

    function updateLineTotals() {
        $(".product_quantity input").each(function () {
            const quantity = parseInt($(this).val()) || 0;
            const row = $(this).closest("tr");
            const price = parseVND(row.find(".product-price").text());
            const total = price * quantity;

            row.find(".product_total").text(total.toFixed(2) + " VND");

            const id_variant = row.find(".product_checkbox").val();
            const id_user = window.user?.id || null;

            if (id_user && id_variant) {
                console.log(id_user, id_variant);
                $.post("/client/carts/update", {
                    id_variant: id_variant,
                    quantity: quantity,
                }).fail(function (xhr) {
                    console.error("Lỗi cập nhật giỏ hàng:", xhr.responseText);
                });
            }
        });
        updateCartTotals();
    }

    function updateCartTotals() {
        let cartTotal = 0;
        $("tbody .product_checkbox:checked").each(function () {
            const productTotal = parseVND(
                $(this).closest("tr").find(".product_total").text()
            );
            cartTotal += productTotal;
        });

        const shipping = parseVND($(".cart_subtotal .shipping").text());
        const grandTotal = cartTotal + shipping;

        $(".cart_subtotal .cart_amount")
            .first()
            .text(cartTotal.toFixed(2) + " VND");
        $(".cart_subtotal .cart_amount")
            .last()
            .text(grandTotal.toFixed(2) + " VND");
    }

    function removeCartItem(id_variant) {
        $.post("/client/carts/remove", { id_variant });
    }

    function bindCheckoutHandler() {
        $(".checkout_btn").on("click", function (e) {
            e.preventDefault();
            const data = buildCartData();

            $.post("/client/checkout", { data }, function (response) {
                window.location.href = response.link;
            }).fail(function (xhr) {
                console.error(xhr.responseText);
            });
        });
    }

    function buildCartData() {
        const cartItems = [];
        const cart = {};
        const total = parseVND($(".cart_subtotal .cart_amount").last().text());
        const shipping = parseVND($(".cart_subtotal .shipping").text());

        cart.total = total;
        cart.subTotal = total - shipping;
        cart.shipping = shipping;
        cart.id_user = window.user?.id || null;

        $("tbody tr").each(function () {
            const checkbox = $(this).find(".product_checkbox");
            if (checkbox.prop("checked")) {
                cartItems.push({
                    id_variant: checkbox.val(),
                    quantity: $(this).find(".product_quantity input").val(),
                    price: parseVND($(this).find(".product-price").text()),
                    product_total: parseVND(
                        $(this).find(".product_total").text()
                    ),
                    name: $(this).find(".product_name a").text().trim(),
                    image: $(this).find(".product_thumb img").attr("src"),
                    sku: $(this).find(".sku").text().replace("sku:", "").trim(),
                });
            }
        });

        return { details: cartItems, cart };
    }

    $(".product_quantity input").on("change", updateLineTotals);

    $("#select_all").on("click", function () {
        $("tbody .product_checkbox").prop("checked", this.checked);
        updateCartTotals();
    });

    $("tbody .product_checkbox").on("change", function () {
        const allChecked =
            $("tbody .product_checkbox:checked").length ===
            $("tbody .product_checkbox").length;
        $("#select_all").prop("checked", allChecked);
        updateCartTotals();
    });

    $("tbody .product_remove a").on("click", function (e) {
        e.preventDefault();
        const row = $(this).closest("tr");
        const id_variant = row.find(".product_checkbox").val();
        removeCartItem(id_variant);
        row.remove();
        updateLineTotals();
    });

    updateLineTotals();
    bindCheckoutHandler();
});
