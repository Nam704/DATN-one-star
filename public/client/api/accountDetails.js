$(document).ready(function () {
    console.log("this is account details");
    var csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

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
