$(document).ready(function () {
    console.log("Address JS initialized");

    // Load provinces on page load
    loadProvinces();

    // Function to load provinces
    function loadProvinces(callback) {
        // Lưu giá trị province đã chọn (nếu có)
        var preSelectedProvince = $("#province").val();

        $.ajax({
            type: "GET",
            url: "/api/address/provinces",
            dataType: "json",
            success: function (response) {
                var provinceSelect = $("#province");
                provinceSelect.html('<option value="">Select Province/City</option>');
                $.each(response, function (key, value) {
                    provinceSelect.append(
                        '<option value="' + value.id + '">' + value.name + '</option>'
                    );
                });
                // Nếu có giá trị đã được chọn từ trước, set lại nó
                if (preSelectedProvince) {
                    provinceSelect.val(preSelectedProvince);
                }
                provinceSelect.prop("disabled", false);
                if (typeof callback === "function") {
                    callback();
                }
            },
            error: function (error) {
                console.error("Error loading provinces:", error);
            },
        });
    }

    // When selecting a province, load districts
    $("#province").change(function () {
        var provinceId = $(this).val();

        // Reset district and ward dropdowns
        $("#district").html('<option value="">Select District</option>');
        $("#ward").html('<option value="">Select Ward</option>');

        // Disable ward dropdown until district is selected
        $("#ward").prop("disabled", true);

        if (provinceId) {
            // Enable district dropdown
            $("#district").prop("disabled", false);

            $.ajax({
                url: "/api/address/districts/" + provinceId,
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
                error: function (error) {
                    console.error("Error loading districts:", error);
                    $("#district").prop("disabled", true);
                },
            });
        } else {
            // If no province selected, disable district dropdown
            $("#district").prop("disabled", true);
        }
    });

    // When selecting a district, load wards
    $("#district").change(function () {
        var districtId = $(this).val();

        // Reset ward dropdown
        $("#ward").html('<option value="">Select Ward</option>');

        if (districtId) {
            // Enable ward dropdown
            $("#ward").prop("disabled", false);

            $.ajax({
                url: "/api/address/wards/" + districtId,
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
                error: function (error) {
                    console.error("Error loading wards:", error);
                    $("#ward").prop("disabled", true);
                },
            });
        } else {
            // If no district selected, disable ward dropdown
            $("#ward").prop("disabled", true);
        }
    });
});
