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
                // Xử lý lỗi validate
                if (response.data.errors) {
                    // Lấy tất cả thông báo lỗi và nối thành chuỗi
                    const errorMessages = Object.values(response.data.errors)
                        .flat()
                        .join("\n");
                    GlobalUtils.showNotification(errorMessages, {
                        backgroundColor: "#ff4444",
                    });
                } else {
                    GlobalUtils.showNotification(
                        response.data.message || "Lỗi khi tải dữ liệu",
                        { backgroundColor: "#ff4444" }
                    );
                }
            }
        })
        .catch((error) => {
            console.error("Lỗi khi tải dữ liệu:", error);
            let errorMessage = "Có lỗi xảy ra khi tải dữ liệu";
            if (
                error.response &&
                error.response.data &&
                error.response.data.errors
            ) {
                // Xử lý lỗi từ server (nếu có)
                errorMessage = Object.values(error.response.data.errors)
                    .flat()
                    .join("\n");
            }
            GlobalUtils.showNotification(errorMessage, {
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
    const today = new Date().toISOString().split("T")[0];
    if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
        GlobalUtils.showNotification(
            "Ngày bắt đầu không thể sau ngày kết thúc",
            { backgroundColor: "#ff4444" }
        );
        return;
    }
    if (dateFrom && new Date(dateFrom) > new Date(today)) {
        GlobalUtils.showNotification(
            "Ngày bắt đầu không được trong tương lai",
            { backgroundColor: "#ff4444" }
        );
        return;
    }
    if (dateTo && new Date(dateTo) > new Date(today)) {
        GlobalUtils.showNotification(
            "Ngày kết thúc không được trong tương lai",
            { backgroundColor: "#ff4444" }
        );
        return;
    }

    // Gửi yêu cầu AJAX qua Axios
    loadOrders(`${GlobalUtils.baseUrl}/admin/orders/list`, formData);

    // Đóng modal
    $("#filterModal").modal("hide");
});
