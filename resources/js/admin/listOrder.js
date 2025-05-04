import Echo from "laravel-echo";
import "../app.js";

$(document).ready(function () {
    // Kiểm tra khởi tạo Echo
    if (!window.Echo) {
        console.error("Echo is not initialized!");
        return;
    }

    // Lắng nghe sự kiện AdminNotification
    window.Echo.private("admin-notifications").listen(
        "AdminNotification",
        (event) => {
            console.log("New Admin Notification:", event);

            // Chỉ cập nhật nếu thông báo liên quan đến đơn hàng
            if (event.category === "order") {
                // Lấy bộ lọc hiện tại từ form
                const formData = $("#filterForm")
                    .serializeArray()
                    .reduce((obj, item) => {
                        obj[item.name] = item.value || null;
                        return obj;
                    }, {});

                // Tải lại danh sách đơn hàng
                loadOrders(`${GlobalUtils.baseUrl}/admin/orders/list`, formData)
                    .then(() => {
                        // GlobalUtils.showNotification(
                        //     event.message ||
                        //         "Có đơn hàng mới, danh sách đã được cập nhật!",
                        //     { backgroundColor: "#00b09b" }
                        // );
                    })
                    .catch((error) => {
                        console.error("Error reloading orders:", error);
                        GlobalUtils.showNotification(
                            "Lỗi khi tải lại danh sách đơn hàng",
                            { backgroundColor: "#ff4444" }
                        );
                    });
            }
        }
    );

    // Hàm gửi yêu cầu AJAX để tải dữ liệu
    function loadOrders(url, params = {}) {
        return axios
            .get(url, { params })
            .then((response) => {
                if (response.data.success) {
                    $("#ordersTable tbody").html(response.data.html);
                    $("#pagination").html(response.data.pagination);
                    // Có thể bỏ comment nếu muốn thông báo khi tải thành công
                    // GlobalUtils.showNotification("Đã tải dữ liệu thành công", {
                    //     backgroundColor: "#00b09b",
                    // });
                } else {
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

    // Xử lý gửi form lọc (giữ nguyên code của bạn)
    $("#filterForm").on("submit", function (e) {
        e.preventDefault();
        const formData = $(this)
            .serializeArray()
            .reduce((obj, item) => {
                obj[item.name] = item.value || null;
                return obj;
            }, {});

        // Kiểm tra hợp lệ dữ liệu phía client (giữ nguyên)
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
        if (search && search.length > 255) {
            GlobalUtils.showNotification(
                "Từ khóa tìm kiếm không được vượt quá 255 ký tự",
                { backgroundColor: "#ff4444" }
            );
            return;
        }

        loadOrders(
            `${GlobalUtils.baseUrl}/admin/orders/list`,
            formData
        ).finally(() => {
            $("#filterModal").modal("hide");
        });
    });

    // Xử lý nút "Xem tất cả" (giữ nguyên)
    $("#clearDateFilters").on("click", function () {
        $('input[name="date_from"]').val("");
        $('input[name="date_to"]').val("");
        GlobalUtils.showNotification("Đã xóa giới hạn thời gian", {
            backgroundColor: "#00b09b",
        });
        // $("#filterForm").trigger("submit");
    });

    // Xử lý nút "Clear" trong modal (giữ nguyên)
    $("#clearFilters").on("click", function () {
        $("#filterForm")[0].reset();
        GlobalUtils.showNotification("Đã xóa tất cả bộ lọc", {
            backgroundColor: "#00b09b",
        });
        // $("#filterForm").trigger("submit");
    });

    // Xử lý click vào các nút phân trang (giữ nguyên)
    $(document).on("click", "#pagination .pagination a", function (e) {
        e.preventDefault();
        const url = $(this).attr("href");
        loadOrders(url);
    });
    $(document).on("click", ".approve-cancellation", function (e) {
        e.preventDefault();
        const orderId = $(this).data("id");
        const $button = $(this);

        // Vô hiệu hóa nút và thay đổi văn bản khi đang xử lý
        $button.prop("disabled", true).text("Đang xử lý...");

        // Gửi yêu cầu POST bằng Axios
        axios
            .post(
                `${GlobalUtils.baseUrl}/admin/orders/` +
                    orderId +
                    `/process-cancellation`,
                {
                    action: "approve", // Dữ liệu gửi đi, tương ứng với input hidden trong form cũ
                    // Thêm CSRF token để bảo mật (nếu Laravel yêu cầu)
                }
            )
            .then((response) => {
                if (response.data.success) {
                    // Hiển thị thông báo thành công
                    GlobalUtils.showNotification(
                        response.data.message || "Đã chấp nhận hủy đơn!",
                        {
                            backgroundColor: "#00b09b",
                        }
                    );

                    // (Tùy chọn) Cập nhật giao diện nếu cần, ví dụ: thay đổi trạng thái
                    const $row = $(`tr[data-id="${orderId}"]`);
                    const $statusTd = $row.find("td").eq(3); // Cột trạng thái
                    $statusTd.html(`
                        <span class="badge bg-danger">
                            Cancelled
                        </span>
                    `);

                    // Ẩn hoặc xóa nút sau khi chấp nhận
                    $button.remove();
                } else {
                    throw new Error(
                        response.data.message || "Lỗi khi chấp nhận hủy đơn"
                    );
                }
            })
            .catch((error) => {
                console.error("Lỗi khi chấp nhận hủy đơn:", error);
                const errorMessage =
                    error.response?.data?.message ||
                    "Có lỗi xảy ra khi xử lý yêu cầu";
                GlobalUtils.showNotification(errorMessage, {
                    backgroundColor: "#ff4444",
                });
            })
            .finally(() => {
                // Khôi phục nút về trạng thái ban đầu (nếu không ẩn/xóa)
                $button.prop("disabled", false).text("Chấp nhận");
            });
    });
    // Xử lý cập nhật trạng thái đơn hàng (giữ nguyên)
    $(document).on("click", ".update-status", function (e) {
        e.preventDefault();
        const orderId = $(this).data("id");
        const $button = $(this);

        $button.prop("disabled", true).text("Đang xử lý...");
        axios
            .post(
                `${GlobalUtils.baseUrl}/admin/orders/update-status/${orderId}`
            )
            .then((response) => {
                if (response.data.success) {
                    GlobalUtils.showNotification(response.data.message, {
                        backgroundColor: "#00b09b",
                    });

                    // Lấy newStatus từ response
                    var newStatus =
                        response.data.order.order_status.next_status.name;
                    console.log(newStatus);

                    // Tìm hàng <tr> chứa orderId và cập nhật trạng thái trong <td>
                    const $row = $(`tr[data-id="${orderId}"]`);
                    const $statusTd = $row.find("td").eq(3); // Giả sử cột trạng thái là cột thứ 4

                    // Xác định lớp CSS dựa trên trạng thái mới
                    let badgeClass = "bg-warning"; // Mặc định
                    if (newStatus.toLowerCase().includes("delivered")) {
                        badgeClass = "bg-success";
                    } else if (newStatus.toLowerCase().includes("cancelled")) {
                        badgeClass = "bg-danger";
                    }

                    // Cập nhật nội dung và lớp của <span>
                    $statusTd.html(`
                        <span class="badge ${badgeClass}">
                            ${newStatus}
                        </span>
                    `);

                    // Ẩn nút "Cập nhật" nếu newStatus là "Shipping"
                    if (newStatus.toLowerCase() === "shipping") {
                        $row.find(".update-status").hide(); // Ẩn nút trong hàng này
                    }
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
            })
            .finally(() => {
                $button.prop("disabled", false).text("Cập nhật");
            });
    });

    // Xử lý yêu cầu hủy đơn hàng (giữ nguyên)
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
                // $("#filterForm").trigger("submit");
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
