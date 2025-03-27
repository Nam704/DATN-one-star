import Echo from "laravel-echo";
import "./bootstrap";

$(document).ready(function () {
    loadOrder();
    $(document).on("click", ".accept", function (e) {
        e.preventDefault(); // Ngừng hành động mặc định của nút

        var orderId = $(this).data("order-id"); // Lấy ID đơn hàng từ thuộc tính data

        var url = "http://127.0.0.1:8000/admin/orders/update-status/" + orderId;
        $.ajax({
            type: "POST",
            url: url,
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
            },
            success: function (response) {
                // Xử lý khi cập nhật trạng thái thành công
                alert(response.message); // Hiển thị thông báo thành công
                loadOrder();
            },
            error: function (xhr, status, error) {
                // // Xử lý khi có lỗi
                // alert("Có lỗi xảy ra, vui lòng thử lại.");
                console.log(xhr.responseText);
                alert(error);
            },
        });
    });
    window.Echo.private("private-notifications").listen(
        "PrivateNotification",
        (event) => {
            // Cập nhật giao diện thông báo
            loadOrder();
        }
    );
    $("#statuses").change(function (e) {
        e.preventDefault();
        loadOrder();
    });
});
function loadOrder() {
    var status = $("#statuses").val();

    // Xây dựng URL đúng
    var url = "http://127.0.0.1:8000/admin/orders/update-list?status=" + status;

    $.ajax({
        type: "POST",
        url: url,
        data: {
            status: status,
            _token: $("meta[name='csrf-token']").attr("content"),
        },
        dataType: "json",
        success: function (response) {
            console.log(response);

            $(".order_list").html(response.html);
        },
        error: function (xhr, status, error) {
            console.log(error);
        },
    });
}
