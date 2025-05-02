import "../app.js";
$(document).ready(function () {
    $(document).on("click", ".update-status", function (e) {
        e.preventDefault();
        const orderId = $(this).data("id");
        axios
            .post(
                `${GlobalUtils.baseUrl}/admin/orders/update-status/${orderId}`
            )
            .then((response) => {
                if (response.data.success) {
                    GlobalUtils.showNotification(response.data.message, {
                        backgroundColor: "#00b09b",
                    });
                    // Tải lại danh sách đơn hàng
                    $("#filterForm").trigger("submit");
                } else {
                    throw new Error(
                        response.data.message || "Lỗi khi cập nhật trạng thái"
                    );
                }
            })
            .catch((error) => {
                console.error("Lỗi khi cập nhật trạng thái:", error);
                const errorMessage =
                    error.response?.data?.message ||
                    "Có lỗi xảy ra khi cập nhật trạng thái";
                GlobalUtils.showNotification(errorMessage, {
                    backgroundColor: "#ff4444",
                });
            });
    });
});
