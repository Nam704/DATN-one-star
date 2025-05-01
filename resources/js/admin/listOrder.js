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
                    // GlobalUtils.showNotification("Đã tải dữ liệu thành công", {
                    //     backgroundColor: "#00b09b",
                    // });
                } else {
                    // Xử lý lỗi validate
                    if (response.data.errors) {
                        const errorMessages = Object.values(
                            response.data.errors
                        )
                            .flat()
                            .join("\n");
                        throw new Error(errorMessages);
                    } else {
                        throw new Error(
                            response.data.message || "Lỗi khi tải dữ liệu"
                        );
                    }
                }
            })
            .catch((error) => {
                console.error("Lỗi khi tải dữ liệu:", error);
                const errorMessage =
                    error.message ||
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

        // Kiểm tra hợp lệ dữ liệu phía client
        const {
            date_from,
            date_to,
            min_total,
            max_total,
            min_shipping,
            max_shipping,
            search,
        } = formData;
        const today = new Date().toISOString().split("T")[0];
        const twoYearsAgo = new Date();
        twoYearsAgo.setFullYear(twoYearsAgo.getFullYear() - 2);
        const minDate = twoYearsAgo.toISOString().split("T")[0];

        // Kiểm tra ngày
        if (date_from && date_to && new Date(date_from) > new Date(date_to)) {
            GlobalUtils.showNotification(
                "Ngày bắt đầu không thể sau ngày kết thúc",
                { backgroundColor: "#ff4444" }
            );
            return;
        }
        if (date_from && new Date(date_from) > new Date(today)) {
            GlobalUtils.showNotification(
                "Ngày bắt đầu không được trong tương lai",
                { backgroundColor: "#ff4444" }
            );
            return;
        }
        if (date_to && new Date(date_to) > new Date(today)) {
            GlobalUtils.showNotification(
                "Ngày kết thúc không được trong tương lai",
                { backgroundColor: "#ff4444" }
            );
            return;
        }
        if (date_from && new Date(date_from) < new Date(minDate)) {
            GlobalUtils.showNotification(
                "Ngày bắt đầu không được trước 2 năm",
                { backgroundColor: "#ff4444" }
            );
            return;
        }
        if (date_to && new Date(date_to) < new Date(minDate)) {
            GlobalUtils.showNotification(
                "Ngày kết thúc không được trước 2 năm",
                { backgroundColor: "#ff4444" }
            );
            return;
        }

        // Kiểm tra total
        if (
            min_total &&
            max_total &&
            parseFloat(min_total) > parseFloat(max_total)
        ) {
            GlobalUtils.showNotification(
                "Tổng giá trị tối đa phải lớn hơn hoặc bằng tổng giá trị tối thiểu",
                { backgroundColor: "#ff4444" }
            );
            return;
        }
        if (min_total && parseFloat(min_total) < 0) {
            GlobalUtils.showNotification(
                "Tổng giá trị tối thiểu không được âm",
                { backgroundColor: "#ff4444" }
            );
            return;
        }
        if (max_total && parseFloat(max_total) > 100000000) {
            GlobalUtils.showNotification(
                "Tổng giá trị tối đa không được vượt quá 100tr VNĐ",
                { backgroundColor: "#ff4444" }
            );
            return;
        }

        // Kiểm tra shipping
        if (
            min_shipping &&
            max_shipping &&
            parseFloat(min_shipping) > parseFloat(max_shipping)
        ) {
            GlobalUtils.showNotification(
                "Phí vận chuyển tối đa phải lớn hơn hoặc bằng phí vận chuyển tối thiểu",
                { backgroundColor: "#ff4444" }
            );
            return;
        }
        if (min_shipping && parseFloat(min_shipping) < 0) {
            GlobalUtils.showNotification(
                "Phí vận chuyển tối thiểu không được âm",
                { backgroundColor: "#ff4444" }
            );
            return;
        }
        if (max_shipping && parseFloat(max_shipping) > 1000000) {
            GlobalUtils.showNotification(
                "Phí vận chuyển tối đa không được vượt quá 1 triệu VNĐ",
                { backgroundColor: "#ff4444" }
            );
            return;
        }

        // Kiểm tra search
        if (search && search.length > 255) {
            GlobalUtils.showNotification(
                "Từ khóa tìm kiếm không được vượt quá 255 ký tự",
                { backgroundColor: "#ff4444" }
            );
            return;
        }
        if (search && !/^[\w\s@.]+$/.test(search)) {
            GlobalUtils.showNotification(
                "Từ khóa tìm kiếm chỉ được chứa chữ, số, khoảng trắng, @ và .",
                { backgroundColor: "#ff4444" }
            );
            return;
        }

        // Thông báo nếu không chọn ngày
        // if (!date_from && !date_to) {
        //     GlobalUtils.showNotification("Đang hiển thị tất cả đơn hàng", {
        //         backgroundColor: "#00b09b",
        //     });
        // }

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
