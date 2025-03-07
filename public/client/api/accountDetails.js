$(document).ready(function () {
    console.log("this is account details");
    var csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    // processAddress();
    $("#save_address").click(function (e) {
        e.preventDefault();
        var is_default = $("#is_default").val();
        var ward = $("#ward").val();
        var address_detail = $("#address_detail").val();
        $.ajax({
            url: "http://127.0.0.1:8000/client/users/create-address", // Địa chỉ route của bạn
            method: "POST",
            data: {
                address_detail: address_detail,
                is_default: is_default,
                id_ward: ward,
                _token: csrfToken,
            },
            success: function (response) {
                console.log(response); // Dữ liệu phản hồi từ server
            },
            error: function (xhr, status, error) {
                console.error(error); // Lỗi nếu có
                console.log(xhr.responseText);
            },
        });
    });
});
function processAddress() {
    $("#ward").change(function (e) {
        e.preventDefault();
        var ward = $("#ward").val();
        console.log("ward", ward);
    });
}
