$(document).ready(function () {
    // Khi chọn tỉnh
    $("#province").click(function (e) {
        $.ajax({
            type: "GET",
            url: "/api/address/provinces",
            dataType: "json",
            success: function (response) {
                $.each(response, function (key, value) {
                    $("#province").append(
                        '<option value="' +
                            value.id +
                            '">' +
                            value.name +
                            "</option>"
                    );
                });
            },
        });
    });
    $("#province").change(function () {
        var provinceId = $(this).val(); // Lấy ID của tỉnh đã chọn

        // Reset quận và phường khi tỉnh thay đổi
        $("#district").html('<option value="">Chọn quận</option>');
        $("#ward").html('<option value="">Chọn phường</option>');

        if (provinceId) {
            $.ajax({
                url: "/api/address/districts/" + provinceId, // API lấy danh sách quận theo tỉnh
                type: "GET",
                dataType: "json",
                success: function (response) {
                    $.each(response, function (key, value) {
                        $("#district").append(
                            '<option value="' +
                                value.id +
                                '">' +
                                value.name +
                                "</option>"
                        );
                    });
                },
            });
        }
    });

    // Khi chọn quận
    $("#district").change(function () {
        var districtId = $(this).val(); // Lấy ID của quận đã chọn

        // Reset phường khi quận thay đổi
        $("#ward").html('<option value="">Chọn phường</option>');

        if (districtId) {
            $.ajax({
                url: "/api/address/wards/" + districtId, // API lấy danh sách phường theo quận
                type: "GET",
                dataType: "json",
                success: function (response) {
                    $.each(response, function (key, value) {
                        $("#ward").append(
                            '<option value="' +
                                value.id +
                                '">' +
                                value.name +
                                "</option>"
                        );
                    });
                },
            });
        }
    });
});
