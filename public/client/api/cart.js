$(document).ready(function () {
    var csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    // Hàm tính tổng sản phẩm cho từng sản phẩm trong giỏ hàng
    console.log("this is cart");

    // Cập nhật tổng giá của từng sản phẩm
    function updateProductTotal() {
        $(".product_quantity input").each(function () {
            // Lấy số lượng sản phẩm và giá sản phẩm
            var quantity = $(this).val();
            var price = $(this)
                .closest("tr")
                .find(".product-price")
                .text()
                .trim(); // Lấy giá mà không cần phải replace "£"

            // Tính tổng giá của sản phẩm
            var total = parseFloat(price) * parseInt(quantity);

            // Cập nhật tổng giá vào ô product_total
            $(this)
                .closest("tr")
                .find(".product_total")
                .text(total.toFixed(2) + "VND"); // Hiển thị tổng theo định dạng tiền tệ
        });

        // Cập nhật tổng giỏ hàng
        updateCartTotal();
    }

    // Hàm tính tổng giỏ hàng
    function updateCartTotal() {
        var cartTotal = 0;

        // Lấy tổng giá của tất cả các sản phẩm được checked trong giỏ hàng
        $("tbody .product_checkbox:checked").each(function () {
            var productTotal = parseFloat(
                $(this).closest("tr").find(".product_total").text().trim()
            );
            cartTotal += productTotal;
        });

        // Cập nhật tổng giỏ hàng
        $(".cart_subtotal .cart_amount")
            .first()
            .text(cartTotal.toFixed(2) + "VND");

        var shipping = $(".cart_subtotal .shipping ").text().trim();
        // Giả sử bạn có phí vận chuyển cố định

        var grandTotal = cartTotal + parseFloat(shipping);

        // Cập nhật tổng cộng của giỏ hàng
        $(".cart_subtotal .cart_amount")
            .last()
            .text(grandTotal.toFixed(2) + "VND");
    }

    // Lắng nghe sự kiện thay đổi số lượng sản phẩm
    $(".product_quantity input").on("change", function () {
        updateProductTotal();
    });

    // Khởi động tính toán khi trang load
    updateProductTotal();

    // Xử lý việc chọn/deselect tất cả các checkbox
    $("#select_all").on("click", function () {
        $("tbody .product_checkbox").prop("checked", $(this).prop("checked"));
        updateCartTotal();
    });

    // Khi thay đổi trạng thái checkbox con
    $("tbody .product_checkbox").on("change", function () {
        // Kiểm tra xem tất cả các checkbox con có được chọn hay không
        var allChecked =
            $("tbody .product_checkbox:checked").length ===
            $("tbody .product_checkbox").length;

        // Cập nhật trạng thái checkbox 'select all' nếu tất cả các checkbox con đã được chọn
        $("#select_all").prop("checked", allChecked);

        // Cập nhật tổng giỏ hàng khi checkbox thay đổi
        updateCartTotal();
    });

    // Xóa sản phẩm khi click vào nút Delete
    $("tbody .product_remove a").on("click", function (e) {
        e.preventDefault(); // Ngừng hành động mặc định của link
        var productRow = $(this).closest("tr");
        var id_variant = $(this).closest("tr").find(".product_checkbox").val();
        // alert(id_variant);
        // Nếu sản phẩm được checked, trừ nó khỏi tổng giỏ hàng trước khi xóa
        if (productRow.find(".product_checkbox").prop("checked")) {
            var productTotal = parseFloat(
                productRow.find(".product_total").text().trim()
            );
            var cartTotal = parseFloat(
                $(".cart_subtotal .cart_amount").first().text().trim()
            );

            // Trừ đi tổng của sản phẩm này trước khi xóa
            cartTotal -= productTotal;

            // Cập nhật lại tổng giỏ hàng
            $(".cart_subtotal .cart_amount")
                .first()
                .text(cartTotal.toFixed(2) + "VND");

            var shipping = $(".cart_subtotal .shipping ").text().trim();

            var grandTotal = cartTotal + parseFloat(shipping);

            $(".cart_subtotal .cart_amount")
                .last()
                .text(grandTotal.toFixed(2) + "VND");
        }
        removeFromCart(id_variant, csrfToken);
        // Xóa sản phẩm khỏi giỏ hàng (xóa dòng tương ứng)
        productRow.remove();
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
                // getCart();
            },
            error: function (xhr) {
                alert("Error removing product from cart.");
                console.error(xhr.responseText);
            },
        });
    }
    // Xử lý việc click vào nút Checkout
    function checkout() {
        var cartTotal = parseFloat(
            $(".cart_subtotal .cart_amount").first().text().trim()
        );
        var shipping = $(".cart_subtotal .shipping ").text().trim();
        var grandTotal;
        var checkout = $(".checkout");
    }
});
