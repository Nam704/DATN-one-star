$(document).ready(function () {
    var csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    $("#process").click(function (e) {
        e.preventDefault();
        let userData = {
            name: $("#name_user").val(),
            phone: $("#phone").val(),
            email: $("#email").val(),
            province: $("#province").val(),
            district: $("#district").val(),
            ward: $("#ward").val(),
            address: $("#address_detail").val(),
            order_note: $("#order_note").val(),
            id_address: $("#address").data("address"),
        };
        let paymentMethod = $("input[type='radio']:checked").attr("id");

        console.log(userData, paymentMethod);
        $.ajax({
            url: "http://127.0.0.1:8000/client/orders/store",
            method: "POST",
            data: {
                userData: userData,
                _token: csrfToken,
                payment_method: paymentMethod,
                redirect: 1,
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                console.log(response.redirect_url);
                if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                } else {
                    alert("lỗi khi lấy url");
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
                console.log(xhr.responseText);
            },
        });
    });
});
