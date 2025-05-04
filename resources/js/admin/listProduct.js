// import axios from "axios";
import "../app.js";

$(document).ready(function () {
    // Hàm xóa các lỗi cũ
    function clearErrors() {
        $(".validation-error").remove();
        $(".is-invalid").removeClass("is-invalid");
        $("#filterModal .modal-body .alert").remove();
    }

    $("#applyFilter").on("click", function (e) {
        e.preventDefault();
        clearErrors();

        var errors = {};
        var stock = $("#stock").val();
        var quantity = $("#quantity").val().trim();
        var minPrice = $('input[name="min_price"]').val().trim();
        var maxPrice = $('input[name="max_price"]').val().trim();
        var fromDate = $('input[name="created_from"]').val();
        var toDate = $('input[name="created_to"]').val();
        var sortView = $("#sort_view").val();
        var today = new Date().toISOString().split("T")[0];

        // 1. Validate quantity khi stock = 'quantity'
        if (stock === "quantity") {
            if (!quantity) {
                errors.quantity =
                    "Bạn phải nhập số lượng khi chọn Số lượng cụ thể.";
            } else if (!/^\d+$/.test(quantity) || parseInt(quantity, 10) < 0) {
                errors.quantity = "Số lượng phải là số nguyên từ 0 trở lên.";
            }
        }

        // 2. Validate giá tiền
        if (minPrice && maxPrice) {
            var min = parseFloat(minPrice);
            var max = parseFloat(maxPrice);
            if (isNaN(min) || isNaN(max)) {
                errors.price = "Giá phải là số hợp lệ.";
            } else if (max < min) {
                errors.price =
                    "Giá tối đa phải lớn hơn hoặc bằng giá tối thiểu.";
            }
        }

        // 3. Validate ngày
        if (fromDate && toDate) {
            if (toDate < fromDate) {
                errors.dateRange =
                    "Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.";
            }
        }
        if (toDate && toDate > today) {
            errors.dateFuture = "Ngày kết thúc không được lớn hơn hôm nay.";
        }

        // 4. Validate sort_view
        if (sortView && !["asc", "desc"].includes(sortView)) {
            errors.sort_view = "Kiểu sắp xếp không hợp lệ.";
        }

        // Nếu có lỗi, hiển thị và dừng
        if (Object.keys(errors).length > 0) {
            // Hiển thị lỗi tại modal body (alert đầu)
            $("#filterModal .modal-body").prepend(
                '<div class="alert alert-danger validation-error">Vui lòng sửa các lỗi sau trước khi áp dụng bộ lọc:</div>'
            );

            // Hiển thị chi tiết từng lỗi
            $.each(errors, function (key, msg) {
                var $field = null;
                switch (key) {
                    case "quantity":
                        $field = $("#quantity");
                        break;
                    case "price":
                        $field = $('input[name="max_price"]');
                        break;
                    case "dateRange":
                        $field = $('input[name="created_to"]');
                        break;
                    case "dateFuture":
                        $field = $('input[name="created_to"]');
                        break;
                    case "sort_view":
                        $field = $("#sort_view");
                        break;
                }
                if ($field && $field.length) {
                    $field.addClass("is-invalid");
                    $(
                        '<small class="text-danger validation-error">' +
                            msg +
                            "</small>"
                    ).insertAfter($field);
                }
            });
            return; // Dừng xử lý
        }

        // Nếu không có lỗi, gửi AJAX
        var formData = $("#filterForm").serialize();
        $.ajax({
            url: $("#filterForm").attr("action"),
            method: "GET",
            data: formData,
            success: function (html) {
                $("#fixed-header-datatable tbody").html(html);
                $("#filterModal").modal("hide");
            },
            error: function (xhr) {
                console.error("AJAX error:", xhr);
            },
        });
    });
});
