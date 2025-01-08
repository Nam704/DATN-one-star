$(document).ready(function () {
    $("#province").change(function () {
        let provinceId = $(this).val();
        $("#district").empty().append('<option value="">Chọn quận</option>');
        $("#ward").empty().append('<option value="">Chọn phường</option>');

        if (provinceId) {
            $.ajax({
                url: `/api/address/districts/${provinceId}`,
                type: "GET",
                success: function (districts) {
                    $.each(districts, function (key, district) {
                        $("#district").append(
                            `<option value="${district.id}">${district.name}</option>`
                        );
                    });
                },
            });
        }
    });

    $("#district").change(function () {
        let districtId = $(this).val();
        $("#ward").empty().append('<option value="">Chọn phường</option>');

        if (districtId) {
            $.ajax({
                url: `/api/address/wards/${districtId}`,
                type: "GET",
                success: function (wards) {
                    $.each(wards, function (key, ward) {
                        $("#ward").append(
                            `<option value="${ward.id}">${ward.name}</option>`
                        );
                    });
                },
            });
        }
    });
});
