// import axios from "axios";
import "../app.js";
$(document).ready(function () {
    // Lấy thông tin địa chỉ mặc định từ $user
    const idAddress = $("#address").data("id");

    GlobalAddress.loadUserAddress(idAddress);

    // Xử lý sự kiện submit form
    $("#process").on("click", function (e) {
        e.preventDefault(); // Ngăn chặn submit mặc định
        var data_address = {
            province: $("#province").val(),
            name_province: $("#province option:selected").text(),
            district: $("#district").val(),
            name_district: $("#district option:selected").text(),
            ward: $("#ward").val(),
            name_ward: $("#ward option:selected").text(),
            address_detail: $("#address_detail").val(),
        };
        var data_user = {
            name: $("#name_user").val(),
            phone: $("#phone").val(),
            email: $("#email").val(),
        };
        // Thu thập dữ liệu từ form
        var data_order = {
            order_note: $("#order_note").val(),
            payment_method: $('input[name="payment_method"]:checked').val(),
        };
        var formData = {
            data_user: data_user,
            data_address: data_address,
            data_order: data_order,
        };
        axios
            .post(`${GlobalUtils.baseUrl}/client/orders/store`, formData)
            .then((response) => {
                console.log(response);
                GlobalUtils.showNotification(response.data.message);
                if (response.data.redirect_url) {
                    window.location.href = response.data.redirect_url;
                } else {
                    // Cho COD, chuyển hướng đến trang thành công
                    window.location.href = `${GlobalUtils.baseUrl}/client/users/my-account`;
                }
            })
            .catch((error) => {
                console.log(error);
                GlobalUtils.showNotification(error.response.data.message, {
                    backgroundColor: "#ff4444",
                });
            });
    });
});
