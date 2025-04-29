import "../app.js"; // Import app.js để sử dụng GlobalUtils;
$(document).ready(function () {
    const $retryPaymentBtn = $("#retry-payment");

    $retryPaymentBtn.on("click", function (e) {
        e.preventDefault();
        const orderId = $(this).data("id");
        const $button = $(this);

        // Vô hiệu hóa nút để ngăn nhấp đúp
        $button.prop("disabled", true);
        GlobalUtils.showNotification("Đang xử lý thanh toán...");

        axios
            .post(
                `${GlobalUtils.baseUrl}/client/orders/${orderId}/retry-payment`
            )
            .then((response) => {
                const data = response.data;

                if (data.success && data.code === "PAYMENT_INITIATED") {
                    // Kiểm tra redirectUrl trước khi chuyển hướng
                    console.log(data);
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
                    // Xử lý các mã lỗi cụ thể
                    let errorMessage;
                    switch (data.code) {
                        case "CANNOT_RETRY":
                            errorMessage =
                                data.message ||
                                "Không thể thử lại thanh toán cho đơn hàng này.";
                            break;
                        case "MAX_ATTEMPTS_EXCEEDED":
                            errorMessage =
                                "Đơn hàng đã bị hủy do vượt quá số lần thử thanh toán.";
                            break;
                        case "PAYMENT_INITIATION_FAILED":
                            errorMessage =
                                "Không thể khởi tạo thanh toán. Vui lòng thử lại.";
                            break;
                        case "SYSTEM_ERROR":
                            errorMessage =
                                "Lỗi hệ thống. Vui lòng thử lại sau.";
                            break;
                        default:
                            errorMessage =
                                data.message ||
                                "Lỗi không xác định khi xử lý thanh toán.";
                    }
                    GlobalUtils.showNotification(errorMessage, {
                        backgroundColor: "#ff4444",
                    });
                    $button.prop("disabled", false);
                }
            })
            .catch((error) => {
                // Xử lý lỗi mạng hoặc server (500, 404, v.v.)
                const errorMessage =
                    error.response?.data?.message ||
                    "Lỗi kết nối server. Vui lòng thử lại.";
                GlobalUtils.showNotification(errorMessage, {
                    backgroundColor: "#ff4444",
                });
                $button.prop("disabled", false);
            });
    });
});
