import Echo from "laravel-echo";
import "../app.js";

$(document).ready(function () {
    // Hàm gửi yêu cầu AJAX để tải dữ liệu
    function loadOrders(url, params = {}) {
        return axios
            .get(url, { params })
            .then((response) => {
                if (response.data.success) {
                    $("#ordersTable tbody").html(response.data.html);
                    $("#pagination").html(response.data.pagination);
                    GlobalUtils.showNotification("Đã tải dữ liệu thành công", {
                        backgroundColor: "#00b09b",
                    });
                } else {
                    throw new Error(
                        response.data.message || "Lỗi khi tải dữ liệu"
                    );
                }
            })
            .catch((error) => {
                console.error("Lỗi khi tải dữ liệu:", error);
                const errorMessage =
                    error.response?.data?.message ||
                    "Có lỗi xảy ra khi tải dữ liệu";
                GlobalUtils.showNotification(errorMessage, {
                    backgroundColor: "#ff4444",
                });
                throw error;
            });
    }

    // Xử lý gửi form lọc
    $("#filterForm").on("submit", function (e) {
        e.preventDefault();

        // Lấy dữ liệu từ form và chuẩn hóa thành object
        const formData = $(this)
            .serializeArray()
            .reduce((obj, item) => {
                obj[item.name] = item.value || null;
                return obj;
            }, {});

        // Kiểm tra hợp lệ ngày
        const { date_from, date_to } = formData;
        if (date_from && date_to && new Date(date_from) > new Date(date_to)) {
            GlobalUtils.showNotification(
                "Ngày bắt đầu không thể sau ngày kết thúc",
                { backgroundColor: "#ff4444" }
            );
            return;
        }

        // Thông báo nếu không chọn ngày
        if (!date_from && !date_to) {
            GlobalUtils.showNotification("Đang hiển thị tất cả đơn hàng", {
                backgroundColor: "#00b09b",
            });
        }

        // Gửi yêu cầu AJAX
        loadOrders(
            `${GlobalUtils.baseUrl}/admin/orders/list`,
            formData
        ).finally(() => {
            $("#filterModal").modal("hide");
        });
    });

    // Xử lý nút "Xem tất cả" (xóa date_from và date_to)
    $("#clearDateFilters").on("click", function () {
        $('input[name="date_from"]').val("");
        $('input[name="date_to"]').val("");
        GlobalUtils.showNotification("Đã xóa giới hạn thời gian", {
            backgroundColor: "#00b09b",
        });

        // Tự động gửi lại form sau khi xóa
        $("#filterForm").trigger("submit");
    });

    // Xử lý nút "Clear" trong modal
    $("#clearFilters").on("click", function () {
        $("#filterForm")[0].reset();
        GlobalUtils.showNotification("Đã xóa tất cả bộ lọc", {
            backgroundColor: "#00b09b",
        });
        $("#filterForm").trigger("submit");
    });

    // Xử lý click vào các nút phân trang
    $(document).on("click", "#pagination .pagination a", function (e) {
        e.preventDefault();
        const url = $(this).attr("href");
        loadOrders(url);
    });

    // Xử lý cập nhật trạng thái đơn hàng
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

    // Xử lý yêu cầu hủy đơn hàng
    $(document).on("submit", ".cancel-order-form", function (e) {
        e.preventDefault();
        const form = $(this);
        const action = form.find('input[name="action"]').val();
        const adminNote = form.find('input[name="admin_note"]').val();

        axios
            .post(form.attr("action"), {
                action,
                admin_note: adminNote || null,
            })
            .then((response) => {
                GlobalUtils.showNotification(
                    response.data.message || "Xử lý yêu cầu hủy thành công",
                    {
                        backgroundColor: "#00b09b",
                    }
                );
                // Tải lại danh sách đơn hàng
                $("#filterForm").trigger("submit");
            })
            .catch((error) => {
                console.error("Lỗi khi xử lý yêu cầu hủy:", error);
                const errorMessage =
                    error.response?.data?.message ||
                    "Có lỗi xảy ra khi xử lý yêu cầu hủy";
                GlobalUtils.showNotification(errorMessage, {
                    backgroundColor: "#ff4444",
                });
            });
    });
});
