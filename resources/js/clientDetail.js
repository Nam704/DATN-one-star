$(document).ready(function () {
    console.log("this is account details");
    var csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // Lắng nghe sự kiện từ kênh riêng tư cho người dùng
    window.Echo.private(`notifications.${user.id}`).listen(
        "OrderNotification",
        (event) => {
            // Cập nhật trạng thái đơn hàng trong bảng
            console.log(event.order);
            const orderId = event.order.id; // ID của đơn hàng nhận từ sự kiện
            const orderStatus = event.status; // Trạng thái đơn hàng nhận từ sự kiện

            // Tìm đơn hàng trong bảng và cập nhật trạng thái
            const orderRow = document.getElementById(`order-${orderId}`);
            if (orderRow) {
                const statusCell = orderRow.querySelector(".status");
                if (statusCell) {
                    statusCell.textContent = orderStatus; // Cập nhật trạng thái đơn hàng
                }
            }

            // Hiển thị thông báo cho người dùng
            // alert(event.message);
        }
    );

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
        var addressDetail = $(this).data("detail");
        var wardId = $(this).data("ward");
        var districtId = $(this).data("district");
        var provinceId = $(this).data("province");
        var isDefault = $(this).data("default") == 1;

        $("#address_id").val(addressId);
        $("#address_detail").val(addressDetail);
        $("#is_default").prop("checked", isDefault);

        if (provinceId) {
            setTimeout(function () {
                $("#province").val(provinceId).trigger("change");

                setTimeout(function () {
                    if (districtId) {
                        $("#district").val(districtId).trigger("change");

                        setTimeout(function () {
                            if (wardId) {
                                $("#ward").val(wardId);
                            }
                        }, 500);
                    }
                }, 500);
            }, 100);
        }

        $("#save_address").text("Update Address");
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
