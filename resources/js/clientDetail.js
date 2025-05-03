import "./app.js";
$(document).ready(function () {
    // console.log("this is account details");
    var csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
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

    // Lắng nghe sự kiện từ kênh riêng tư cho người dùng
    window.Echo.private(`notifications.${user.id}`).listen(
        "OrderNotification",
        (event) => {
            // Log để kiểm tra dữ liệu từ sự kiện
            console.log("Order event:", event.order);

            const orderId = event.order.id; // ID của đơn hàng
            const orderStatus = event.status; // Trạng thái đơn hàng từ sự kiện

            // Tìm hàng trong bảng bằng id
            const orderRow = document.getElementById(`order-${orderId}`);
            if (orderRow) {
                const statusCell = orderRow.querySelector(".status"); // Tìm thẻ <span class="status">
                if (statusCell) {
                    // Cập nhật nội dung trạng thái
                    statusCell.textContent = orderStatus;

                    // Cập nhật class của badge dựa trên trạng thái (tùy chọn)
                    if (orderStatus === "Delivered") {
                        statusCell.className = "status badge bg-success";
                    } else if (orderStatus === "Cancelled") {
                        statusCell.className = "status badge bg-danger";
                    } else {
                        statusCell.className = "status badge bg-warning";
                    }
                }
            } else {
                console.warn(`Không tìm thấy hàng với id: order-${orderId}`);
            }

            // Hiển thị thông báo (nếu cần)
            // GlobalUtils.showNotification(event.message);
        }
    );
    $(document).on("click", ".update-status", function (e) {
        e.preventDefault();
        const orderId = $(this).data("id"); // Lấy orderId từ thuộc tính data-id
        const $button = $(this); // Lưu tham chiếu đến nút được nhấn

        // Vô hiệu hóa nút để tránh nhấn nhiều lần
        $button.prop("disabled", true).text("Đang xử lý...");

        // Gửi yêu cầu AJAX bằng Axios
        axios
            .post(`/client/orders/${orderId}/update-status`, {
                _token: csrfToken, // Gửi CSRF token nếu cần
            })
            .then((response) => {
                const data = response.data;

                if (data.success) {
                    // Cập nhật trạng thái trong giao diện
                    const $row = $button.closest("tr"); // Tìm hàng chứa nút
                    const $statusBadge = $row.find(".badge"); // Tìm badge hiển thị trạng thái

                    // Giả sử trạng thái mới là "Delivered" (có thể điều chỉnh theo logic thực tế)
                    $statusBadge
                        .text("Delivered") // Cập nhật văn bản trạng thái
                        .removeClass("bg-warning") // Xóa lớp cũ
                        .addClass("bg-success"); // Thêm lớp mới cho trạng thái Delivered

                    // Xóa nút "Đã nhận" vì trạng thái đã được cập nhật
                    $button.remove();

                    // Hiển thị thông báo thành công
                    GlobalUtils.showNotification(
                        "Cập nhật trạng thái thành công!"
                    );
                } else {
                    // Hiển thị thông báo lỗi từ server
                    alert(data.message || "Không thể cập nhật trạng thái.");
                    $button.prop("disabled", false).text("Đã nhận"); // Khôi phục nút
                }
            })
            .catch((error) => {
                // Xử lý lỗi mạng hoặc server
                const errorMessage =
                    error.response?.data?.message ||
                    "Lỗi hệ thống. Vui lòng thử lại.";
                alert(errorMessage);
                $button.prop("disabled", false).text("Đã nhận"); // Khôi phục nút
            });
    });
    $("#save_address").click(function (e) {
        e.preventDefault();
        var is_default = $("#is_default").is(":checked") ? 1 : 0;
        var ward = $("#ward").val();
        var address_detail = $("#address_detail").val();
        var address_id = $("#address_id").val();

        if (!address_detail) {
            $("#address-alert").html(
                '<div class="alert alert-danger">Please enter address details</div>'
            );
            return;
        }

        if (!ward) {
            $("#address-alert").html(
                '<div class="alert alert-danger">Please select a ward</div>'
            );
            return;
        }

        var url = address_id
            ? "http://127.0.0.1:8000/client/users/update-address"
            : "http://127.0.0.1:8000/client/users/create-address";

        $.ajax({
            url: url,
            method: "POST",
            data: {
                id: address_id,
                address_detail: address_detail,
                is_default: is_default,
                id_ward: ward,
                _token: csrfToken,
            },
            success: function (response) {
                console.log(response);
                $("#address-alert").html(
                    '<div class="alert alert-success">' +
                        response.message +
                        "</div>"
                );

                resetAddressForm();

                $("#save_address").text("Save Address");

                refreshAddressList();
            },
            error: function (xhr, status, error) {
                console.error(error);
                console.log(xhr.responseText);
                $("#address-alert").html(
                    '<div class="alert alert-danger">Error: ' +
                        (xhr.responseJSON?.message || error) +
                        "</div>"
                );
            },
        });
    });

    $(document).on("click", ".edit-address", function () {
        var addressId = $(this).data("id");
        var wardId = $(this).data("ward");
        var provinceId = $(this).data("province");

        // Gửi yêu cầu AJAX để lấy thông tin chi tiết địa chỉ
        $.ajax({
            type: "get",
            url: "http://127.0.0.1:8000/api/address/details",
            data: {
                id: addressId,
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                if (response) {
                    var data = response;

                    // Điền thông tin vào form
                    $("#address_id").val(addressId);
                    $("#address_detail").val(data.address_detail);

                    // 1. Tải danh sách tỉnh (provinces) từ API và điền vào dropdown #province
                    $.ajax({
                        type: "GET",
                        url: "/api/address/provinces", // API lấy danh sách tỉnh
                        dataType: "json",
                        success: function (provinces) {
                            // Xóa các options cũ trong dropdown province
                            $("#province").html(
                                '<option value="">Chọn Tỉnh/Thành phố</option>'
                            );
                            // Thêm các options vào dropdown #province
                            $.each(provinces, function (key, province) {
                                var selected =
                                    province.id == data.province_id
                                        ? "selected"
                                        : "";
                                $("#province").append(
                                    '<option value="' +
                                        province.id +
                                        '" ' +
                                        selected +
                                        ">" +
                                        province.name +
                                        "</option>"
                                );
                            });
                            // Kích hoạt dropdown #province
                            $("#province").prop("disabled", false);

                            // Sau khi province đã được cập nhật, tiếp tục tải district
                            loadDistricts(data.province_id, data.district_id);
                        },
                    });

                    // 3. Kiểm tra nếu địa chỉ này là mặc định, đánh dấu checkbox
                    if (data.is_default) {
                        $("#is_default").prop("checked", true);
                    } else {
                        $("#is_default").prop("checked", false);
                    }
                } else {
                    alert("Không tìm thấy dữ liệu địa chỉ.");
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                alert("Có lỗi khi lấy dữ liệu địa chỉ.");
            },
        });

        // Hàm tải district từ API
        function loadDistricts(provinceId, selectedDistrictId) {
            // Gửi yêu cầu AJAX để lấy danh sách district cho tỉnh đã chọn
            $.ajax({
                url: "/api/address/districts/" + provinceId, // API lấy danh sách quận theo tỉnh
                type: "GET",
                dataType: "json",
                success: function (districts) {
                    // Xóa các options cũ trong dropdown district
                    $("#district").html(
                        '<option value="">Chọn Quận/Huyện</option>'
                    );
                    // Thêm các options vào dropdown #district
                    $.each(districts, function (key, district) {
                        var selected =
                            district.id == selectedDistrictId ? "selected" : "";
                        $("#district").append(
                            '<option value="' +
                                district.id +
                                '" ' +
                                selected +
                                ">" +
                                district.name +
                                "</option>"
                        );
                    });
                    // Kích hoạt dropdown #district
                    $("#district").prop("disabled", false);

                    // Sau khi district đã được cập nhật, tiếp tục tải ward
                    loadWards(selectedDistrictId);
                },
            });
        }

        // Hàm tải wards từ API
        function loadWards(districtId) {
            // Gửi yêu cầu AJAX để lấy danh sách ward cho quận đã chọn
            $.ajax({
                url: "/api/address/wards/" + districtId, // API lấy danh sách phường theo quận
                type: "GET",
                dataType: "json",
                success: function (wards) {
                    // Xóa các options cũ trong dropdown ward
                    $("#ward").html('<option value="">Chọn Phường/Xã</option>');
                    // Thêm các options vào dropdown #ward
                    $.each(wards, function (key, ward) {
                        var selected = ward.id == wardId ? "selected" : "";
                        $("#ward").append(
                            '<option value="' +
                                ward.id +
                                '" ' +
                                selected +
                                ">" +
                                ward.name +
                                "</option>"
                        );
                    });
                    // Kích hoạt dropdown #ward
                    $("#ward").prop("disabled", false);
                },
            });
        }
    });

    $(document).on("click", ".delete-address", function () {
        if (!confirm("Are you sure you want to delete this address?")) {
            return;
        }

        var addressId = $(this).data("id");

        $.ajax({
            url: "http://127.0.0.1:8000/client/users/delete-address",
            method: "POST",
            data: {
                id: addressId,
                _token: csrfToken,
            },
            success: function (response) {
                $("#address-alert").html(
                    '<div class="alert alert-success">' +
                        response.message +
                        "</div>"
                );

                refreshAddressList();
            },
            error: function (xhr, status, error) {
                console.error(error);
                $("#address-alert").html(
                    '<div class="alert alert-danger">Error: ' +
                        (xhr.responseJSON?.message || error) +
                        "</div>"
                );
            },
        });
    });

    $(document).on("click", ".set-default-address", function () {
        var addressId = $(this).data("id");

        $.ajax({
            url: "http://127.0.0.1:8000/client/users/set-default-address",
            method: "POST",
            data: {
                id: addressId,
                _token: csrfToken,
            },
            success: function (response) {
                $("#address-alert").html(
                    '<div class="alert alert-success">' +
                        response.message +
                        "</div>"
                );

                refreshAddressList();
            },
            error: function (xhr, status, error) {
                console.error(error);
                $("#address-alert").html(
                    '<div class="alert alert-danger">Error: ' +
                        (xhr.responseJSON?.message || error) +
                        "</div>"
                );
            },
        });
    });

    $(document).on("click", "#cancel-edit", function () {
        resetAddressForm();

        $("#save_address").text("Save Address");
    });

    function resetAddressForm() {
        $("#address_id").val("");
        $("#address_detail").val("");
        $("#is_default").prop("checked", false);
        $("#province").val("").trigger("change");
        $("#address-alert").html("");
    }

    function refreshAddressList() {
        $.ajax({
            url: "http://127.0.0.1:8000/client/users/get-addresses",
            method: "GET",
            success: function (response) {
                var tableBody = $("#address-list-table tbody");
                tableBody.empty();

                if (response.addresses.length === 0) {
                    tableBody.append(
                        '<tr><td colspan="3" class="text-center">No addresses found</td></tr>'
                    );
                } else {
                    $.each(response.addresses, function (index, address) {
                        var addressHTML =
                            '<tr id="address-' +
                            address.id +
                            '">' +
                            "<td>" +
                            address.address_detail;

                        if (address.ward) {
                            addressHTML +=
                                ", " +
                                address.ward.name +
                                ", " +
                                address.ward.district.name +
                                ", " +
                                address.ward.district.province.name;
                        }

                        addressHTML +=
                            "</td>" +
                            "<td>" +
                            (address.is_default ? "Yes" : "No") +
                            "</td>" +
                            "<td>" +
                            '<button class="btn btn-sm btn-primary edit-address" ' +
                            'data-id="' +
                            address.id +
                            '" ' +
                            'data-detail="' +
                            address.address_detail +
                            '" ' +
                            'data-ward="' +
                            (address.ward ? address.ward.id : "") +
                            '" ' +
                            'data-district="' +
                            (address.ward ? address.ward.district.id : "") +
                            '" ' +
                            'data-province="' +
                            (address.ward
                                ? address.ward.district.province.id
                                : "") +
                            '" ' +
                            'data-default="' +
                            address.is_default +
                            '">' +
                            "Edit</button> ";

                        // Add Set Default button only for non-default addresses
                        if (!address.is_default) {
                            addressHTML +=
                                '<button class="btn btn-sm btn-success set-default-address" data-id="' +
                                address.id +
                                '">Set Default</button> ';
                        }

                        addressHTML +=
                            '<button class="btn btn-sm btn-danger delete-address" data-id="' +
                            address.id +
                            '">Delete</button>' +
                            "</td>" +
                            "</tr>";

                        tableBody.append(addressHTML);
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error refreshing address list:", error);
                $("#address-alert").html(
                    '<div class="alert alert-danger">Error refreshing address list: ' +
                        error +
                        "</div>"
                );
            },
        });
    }

    updateUser(csrfToken);
});

function updateUser(csrfToken) {
    $(".form-details").submit(function (event) {
        event.preventDefault(); // Ngăn chặn reload trang khi submit form

        // Lấy giá trị từ các input
        var id = $("#user_id").val();
        var fullName = $("#FullName").val();
        var email = $("#Email").val();
        var oldPassword = $("input[name='old_password']").val();
        var phone = $("#phone").val();
        var newPassword = $("#new_password").val();
        var new_password_confirmation = $("#new_password_confirmation").val();

        // Đưa các giá trị vào object để xử lý
        var formData = {
            id: id,
            name: fullName,
            email: email,
            old_password: oldPassword,
            phone: phone,
            new_password: newPassword,
            new_password_confirmation: new_password_confirmation,
        };

        // console.log(formData);

        // Gửi dữ liệu bằng AJAX (nếu cần)
        $.ajax({
            url: "http://127.0.0.1:8000/client/users/update", // Thay thế bằng URL xử lý form
            type: "POST",
            data: {
                id: id,
                name: fullName,
                email: email,
                old_password: oldPassword,
                phone: phone,
                new_password: newPassword,
                new_password_confirmation: new_password_confirmation,
                _token: csrfToken,
            },

            success: function (response) {
                // alert("Dữ liệu đã được gửi thành công!");
                console.log(response);
                alert(response.message);
            },
            error: function (xhr, status, error) {
                console.log(error);
                console.log(xhr.responseJSON.message);

                // alert(xhr.responseJSON.message);
            },
        });
    });
}
