import "../app.js"; // Import app.js để sử dụng GlobalUtils
$(document).ready(function () {
    window.Echo.private(`notifications.${user.id}`).listen(
        "OrderNotification",
        (event) => {
            console.log("Order event:", event.order);

            const orderId = event.order.id;
            const orderStatus = event.order.order_status.name.toLowerCase();
            const $orderTitle = $("#order-title");
            const $statusBadge = $orderTitle.find(".order-status");
            const $orderActions = $("#order-actions");

            // Cập nhật trạng thái đơn hàng
            if ($statusBadge.length) {
                $statusBadge.text(orderStatus);
                $statusBadge.removeClass(
                    "bg-info bg-success bg-danger bg-warning"
                );
                switch (orderStatus) {
                    case "delivered":
                        $statusBadge.addClass("bg-success");
                        break;
                    case "cancelled":
                    case "cancel approved":
                        $statusBadge.addClass("bg-danger");
                        break;
                    case "shipping":
                        $statusBadge.addClass("bg-warning");
                        break;
                    default:
                        $statusBadge.addClass("bg-info");
                }
            } else {
                console.warn(`Status badge not found for order: ${orderId}`);
            }

            // Cập nhật khu vực Hành động đơn hàng
            if ($orderActions.length) {
                // Ẩn tất cả hành động trước khi kiểm tra
                $orderActions.find(".retry-payment-btn").hide();
                $orderActions.find(".retry-payment-message").hide();
                $orderActions.find(".received-btn").hide();
                $orderActions.find(".cancel-order-form").hide();

                // Logic hiển thị dựa trên trạng thái mới
                switch (orderStatus) {
                    case "payment failed":
                    case "payment expired":
                    case "payment retry requested":
                        if (event.order.can_retry_payment) {
                            // Giả sử server gửi thêm thông tin này
                            $orderActions.find(".retry-payment-btn").show();
                        } else {
                            $orderActions.find(".retry-payment-message").show();
                        }
                        break;
                    case "shipping":
                        $orderActions.find(".received-btn").show();
                        break;
                    case "pending": // Giả sử đây là trạng thái có thể hủy
                        if (event.order.can_cancel) {
                            // Giả sử server gửi thêm thông tin này
                            $orderActions.find(".cancel-order-form").show();
                        }
                        break;
                    case "delivered":
                    case "cancelled":
                    case "cancel approved":
                        // Không hiển thị hành động nào
                        break;
                }
            }

            if (event.message) {
                GlobalUtils.showNotification(event.message);
            }
        }
    );

    // Xử lý nút Thanh toán lại
    const $retryPaymentBtn = $("#retry-payment");
    $retryPaymentBtn.on("click", function (e) {
        e.preventDefault();
        const orderId = $(this).data("id");
        const $button = $(this);

        $button.prop("disabled", true);
        GlobalUtils.showNotification("Đang xử lý thanh toán...");

        axios
            .post(
                `${GlobalUtils.baseUrl}/client/orders/${orderId}/retry-payment`
            )
            .then((response) => {
                const data = response.data;
                if (data.success && data.code === "PAYMENT_INITIATED") {
                    if (data.redirectUrl) {
                        window.location.href = data.redirectUrl;
                    } else {
                        GlobalUtils.showNotification(
                            "Lỗi: Không nhận được URL thanh toán từ server.",
                            { backgroundColor: "#ff4444" }
                        );
                        $button.prop("disabled", false);
                    }
                } else {
                    let errorMessage;
                    switch (data.code) {
                        case "CANNOT_RETRY":
                            errorMessage =
                                data.message || "Không thể thử lại thanh toán.";
                            break;
                        case "MAX_ATTEMPTS_EXCEEDED":
                            errorMessage = "Đã vượt quá số lần thử thanh toán.";
                            break;
                        case "PAYMENT_INITIATION_FAILED":
                            errorMessage = "Không thể khởi tạo thanh toán.";
                            break;
                        case "SYSTEM_ERROR":
                            errorMessage = "Lỗi hệ thống.";
                            break;
                        default:
                            errorMessage =
                                data.message || "Lỗi không xác định.";
                    }
                    GlobalUtils.showNotification(errorMessage, {
                        backgroundColor: "#ff4444",
                    });
                    $button.prop("disabled", false);
                }
            })
            .catch((error) => {
                const errorMessage =
                    error.response?.data?.message || "Lỗi kết nối server.";
                GlobalUtils.showNotification(errorMessage, {
                    backgroundColor: "#ff4444",
                });
                $button.prop("disabled", false);
            });
    });
});
