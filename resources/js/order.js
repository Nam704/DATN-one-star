import Echo from "laravel-echo";
import "./app.js";
$(document).ready(function () {
    // Hàm gửi yêu cầu AJAX để tải dữ liệu
    function loadOrders(url, params) {
        axios
            .get(url, {
                params: params,
            })
            .then((response) => {
                console.log("Response:", response.data);
                if (response.data.success) {
                    $("#ordersTable tbody").html(response.data.html);
                    $("#pagination").html(response.data.pagination);
                    GlobalUtils.showNotification("Đã tải dữ liệu thành công", {
                        backgroundColor: "#00b09b",
                    });
                } else {
                    GlobalUtils.showNotification(
                        response.data.message || "Lỗi khi tải dữ liệu",
                        { backgroundColor: "#ff4444" }
                    );
                }
            })
            .catch((error) => {
                console.error("Lỗi khi tải dữ liệu:", error);
                GlobalUtils.showNotification("Có lỗi xảy ra khi tải dữ liệu", {
                    backgroundColor: "#ff4444",
                });
            });
    }

    // Xử lý gửi form lọc
    $("#filterForm").on("submit", function (e) {
        e.preventDefault();

        // Lấy dữ liệu từ form và chuẩn hóa thành object
        let formDataArray = $(this).serializeArray();
        let formData = {};
        formDataArray.forEach((item) => {
            formData[item.name] = item.value || null;
        });

        // Kiểm tra hợp lệ ngày
        const dateFrom = formData.date_from;
        const dateTo = formData.date_to;
        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
            GlobalUtils.showNotification(
                "Ngày bắt đầu không thể sau ngày kết thúc",
                { backgroundColor: "#ff4444" }
            );
            return;
        }

        // Thông báo nếu không chọn ngày (hiển thị tất cả đơn hàng)
        if (!dateFrom && !dateTo) {
            GlobalUtils.showNotification("Đang hiển thị tất cả đơn hàng", {
                backgroundColor: "#00b09b",
            });
        }

        // Gửi yêu cầu AJAX qua Axios
        loadOrders(`${GlobalUtils.baseUrl}/admin/orders/list`, formData);

        // Đóng modal
        $("#filterModal").modal("hide");
    });

    // Xử lý nút "Xem tất cả" (xóa date_from và date_to)
    $("#clearDateFilters").on("click", function () {
        $('input[name="date_from"]').val("");
        $('input[name="date_to"]').val("");
        GlobalUtils.showNotification("Đã xóa giới hạn thời gian", {
            backgroundColor: "#00b09b",
        });
    });

    // Xử lý click vào các nút phân trang
    $(document).on("click", "#pagination .pagination a", function (e) {
        e.preventDefault();
        const url = $(this).attr("href");
        loadOrders(url, {});
    });
});
