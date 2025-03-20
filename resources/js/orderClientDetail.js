import Echo from "laravel-echo";
import "./bootstrap";
import { Toast } from "bootstrap";

$(document).ready(function () {
    var orderId = $("#order_id").val();
    console.log(orderId);
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
            success: function (response) {
                Toastify({
                    text: `New notification: ${response.message}`,
                    duration: 3000, // Hiển thị trong 3 giây
                    gravity: "top", // Vị trí: trên cùng
                    position: "right", // Vị trí: bên phải
                    backgroundColor:
                        "linear-gradient(to right, #00b09b, #96c93d)",
                    stopOnFocus: true, // Dừng khi hover vào popup
                    close: true, // Tự động đóng sau khi hiển thị
                }).showToast();
            },
            error: function (xhr, status, error) {
                console.error(error);
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
