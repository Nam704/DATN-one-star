import Echo from "laravel-echo";
import "./bootstrap";
import { Toast } from "bootstrap";

$(document).ready(function () {
    var orderId = $("#order_id").val();
    console.log(orderId);
    $("#retry_payment").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: "Post",
            url: "http://127.0.0.1:8000/client/orders/retry-payment",
            data: {
                _token: csrfToken,
                order_id: orderId,
            },
            dataType: "json",
            success: function (response) {
                console.log(response.data);
                if (response.message == "success") {
                    alert("Đã gửi yêu cầu thanh toán lại");
                    // window.location.href = response.data;
                }
                window.location.href = response.data;
            },
            error: function (xhr, status, error) {
                console.log(error);
                console.log(xhr.responseText);
            },
        });
    });
    $(".cancel").click(function (e) {
        e.preventDefault();
        showListReason();
    });
    $("#exit_cancel_order").click(function (e) {
        e.preventDefault();
        hideListReason();
    });
    $("#confirm_cancel_order").click(function (e) {
        e.preventDefault();
        // alert("Đã hủy đơn hàng");
        var id_reason = $("#id_reason").val();
        $.ajax({
            type: "post",
            url: "http://127.0.0.1:8000/client/orders/cancel",
            data: {
                _token: csrfToken,
                id_reason: id_reason,
                id_order: orderId,
            },
            dataType: "json",
            // success: function (response) {
            //     Toastify({
            //         text: `New notification: ${response.message}`,
            //         duration: 3000, // Hiển thị trong 3 giây
            //         gravity: "top", // Vị trí: trên cùng
            //         position: "right", // Vị trí: bên phải
            //         backgroundColor:
            //             "linear-gradient(to right, #00b09b, #96c93d)",
            //         stopOnFocus: true, // Dừng khi hover vào popup
            //         close: true, // Tự động đóng sau khi hiển thị
            //     }).showToast();
            // },
            error: function (xhr, status, error) {
                console.error(error);
                console.log(xhr.responseText);
            },
        });
    });
});
function showListReason() {
    $("#list-reason").show();
    $(".cancel").hide();
}
function hideListReason() {
    $("#list-reason").hide();
    $(".cancel").show();
}
