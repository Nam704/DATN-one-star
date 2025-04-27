import "../app.js"; // Import app.js để sử dụng GlobalUtils;
$(document).ready(function () {
    const $retryPaymentBtn = $("#retry-payment");

    $retryPaymentBtn.on("click", function (e) {
        var orderId = $(this).data("id");
        e.preventDefault();
        GlobalUtils.showNotification("Đang xử lý thanh toán...", {
            backgroundColor: "#ff4444",
        });
        axios
            .post(
                `${GlobalUtils.baseUrl}/client/orders/${orderId}/retry-payment`
            )
            .then((response) => {
                if (response.data.code === "00") {
                    window.location.href = response.data.redirectUrl;
                } else {
                    GlobalUtils.showNotification("Có lỗi xảy ra!", {
                        backgroundColor: "#ff4444",
                    });
                }
            });
    });
});
