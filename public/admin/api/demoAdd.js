$(document).ready(function () {
    // Template for variant row
    var rowVariant = `
        <div class="row product-variant-template" data-row-id="0">
            <div class="mb-3 col-md-3">
                <label for="product-variant" class="form-label">Product Variant</label>
                <select name="import_details[0][product_variant_id]" data-row-id="0" class="form-control" required>
                </select>
            </div>
            <div class="mb-3 col-md-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" name="import_details[0][quantity]" class="form-control" required>
            </div>
            <div class="mb-3 col-md-3">
                <label for="price_per_unit" class="form-label">Price per Unit</label>
                <input type="number" name="import_details[0][price_per_unit]" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3 col-md-3">
                <label for="expected_price" class="form-label">Expected Price</label>
                <input type="number" name="import_details[0][expected_price]" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3 col-md-12 text-end">
                <button type="button" class="btn btn-danger btn-delete-variant">Delete</button>
            </div>
        </div>
    `;

    // Add single variant row
    $(document).on("click", "#add-row-variant", function () {
        let currentRow = $(this).closest(".import-detail");
        let rowVariantDiv = currentRow.find(".row-variant");
        let productId = currentRow
            .find('select[name^="import_details"][name$="[product]"]')
            .val();

        if (!productId) {
            alert("Please select a product first!");
            return;
        }

        $.ajax({
            type: "get",
            url: `/api/product-variants/${productId}`,
            dataType: "json",
            success: function (response) {
                let currentVariantCount = rowVariantDiv.find(
                    ".product-variant-template"
                ).length;
                if (currentVariantCount >= response.length) {
                    alert("Maximum variants reached!");
                    return;
                }

                let newRow = $(rowVariant);
                newRow.attr("data-row-id", currentVariantCount);
                newRow.find("select, input").each(function () {
                    let name = $(this).attr("name");
                    if (name) {
                        $(this).attr(
                            "name",
                            name.replace("[0]", `[${currentVariantCount}]`)
                        );
                    }
                });

                let variantSelect = newRow.find(
                    'select[name$="[product_variant_id]"]'
                );
                variantSelect
                    .empty()
                    .append('<option value="">Select variant</option>');
                response.forEach((variant) => {
                    variantSelect.append(
                        `<option value="${variant.id}">${variant.sku}</option>`
                    );
                });

                rowVariantDiv.append(newRow);
            },
        });
    });

    // Add all variants
    $(document).on("click", "#add-all-row-variant", function () {
        let currentRow = $(this).closest(".import-detail");
        let rowVariantDiv = currentRow.find(".row-variant");
        let productId = currentRow
            .find('select[name^="import_details"][name$="[product]"]')
            .val();

        if (!productId) {
            alert("Please select a product first!");
            return;
        }

        $.ajax({
            type: "get",
            url: `/api/product-variants/${productId}`,
            dataType: "json",
            success: function (response) {
                rowVariantDiv.empty();

                response.forEach((variant, index) => {
                    let newRow = $(rowVariant);
                    newRow.attr("data-row-id", index);
                    newRow.find("select, input").each(function () {
                        let name = $(this).attr("name");
                        if (name) {
                            $(this).attr(
                                "name",
                                name.replace("[0]", `[${index}]`)
                            );
                        }
                    });

                    let variantSelect = newRow.find(
                        'select[name$="[product_variant_id]"]'
                    );
                    variantSelect
                        .empty()
                        .append(
                            `<option value="${variant.id}">${variant.sku}</option>`
                        );

                    rowVariantDiv.append(newRow);
                });
            },
        });
    });

    // Delete variant row
    $(document).on("click", ".btn-delete-variant", function () {
        $(this).closest(".product-variant-template").remove();
    });
});
$.ajax({
    type: "get",
    url: "/api/products/total",
    dataType: "json",
    success: function (response) {
        var maxRow = response.total;

        let rowIndex = 1;

        $("#add-row-btn").click(function () {
            // Check if all existing rows are filled
            let isValid = true;

            $(".import-detail").each(function () {
                // Check product selection
                let productSelect = $(this).find(
                    'select[name^="import_details"][name$="[product]"]'
                );
                if (!productSelect.val()) {
                    isValid = false;
                    alert(
                        "Vui lòng chọn sản phẩm cho tất cả các dòng trước khi thêm mới"
                    );
                    return false;
                }

                // Check total price
                let totalPrice = $(this).find('input[name$="[total_price]"]');
                if (!totalPrice.val()) {
                    isValid = false;
                    alert(
                        "Vui lòng nhập tổng giá cho tất cả các dòng trước khi thêm mới"
                    );
                    return false;
                }

                // Check all variant rows
                $(this)
                    .find(
                        ".product-variant-template:not(#product-variant-template)"
                    )
                    .each(function () {
                        let variantSelect = $(this).find(
                            'select[name$="[product_variant_id]"]'
                        );
                        let quantity = $(this).find(
                            'input[name$="[quantity]"]'
                        );
                        let pricePerUnit = $(this).find(
                            'input[name$="[price_per_unit]"]'
                        );
                        let expectedPrice = $(this).find(
                            'input[name$="[expected_price]"]'
                        );

                        if (
                            !variantSelect.val() ||
                            !quantity.val() ||
                            !pricePerUnit.val() ||
                            !expectedPrice.val()
                        ) {
                            isValid = false;
                            alert(
                                "Vui lòng điền đầy đủ thông tin cho tất cả các biến thể trước khi thêm mới"
                            );
                            return false;
                        }
                    });

                if (!isValid) return false;
            });

            if (!isValid) return;

            // Existing add row logic continues here...
            let currentRowCount = $(
                "#import-details-container .import-detail"
            ).length;
            if (currentRowCount >= maxRow) {
                alert("Đã đạt số lượng sản phẩm tối đa!");
                return;
            }

            let newRow = $("#import-detail-template").clone();
            newRow.removeAttr("id");

            // Get all currently selected product values
            let selectedProducts = [];
            $('select[name^="import_details"][name$="[product]"]').each(
                function () {
                    let value = $(this).val();
                    if (value) selectedProducts.push(value);
                }
            );

            // Disable selected options in the new row
            let productSelect = newRow.find(
                'select[name^="import_details"][name$="[product]"]'
            );
            productSelect.find("option").each(function () {
                if (selectedProducts.includes($(this).val())) {
                    $(this).prop("disabled", true);
                }
            });

            newRow.find("select, input").each(function () {
                let name = $(this).attr("name");
                $(this).attr("name", name.replace("[0]", `[${rowIndex}]`));
                $(this).attr("data-row-id", rowIndex);
            });

            newRow.find("select").val("");
            newRow.find("input").val("");

            $("#import-details-container").append(newRow);
            rowIndex++;
        });
    },
});

// Add handler for product selection change
$(document).on(
    "change",
    'select[name^="import_details"][name$="[product]"]',
    function () {
        let selectedValue = $(this).val();
        let currentSelect = $(this);

        // Get all product selects
        let allProductSelects = $(
            'select[name^="import_details"][name$="[product]"]'
        );

        // First enable all options in all selects
        allProductSelects.find("option").prop("disabled", false);

        // Then disable selected values in other dropdowns
        allProductSelects.each(function () {
            if (this !== currentSelect[0]) {
                let value = $(this).val();
                if (value) {
                    allProductSelects
                        .find(`option[value="${value}"]`)
                        .prop("disabled", true);
                }
            }
        });
    }
);

$(document).on("click", "#add-row-variant", function () {
    let currentRow = $(this).closest(".import-detail");
    var rowId = $(this).data("row-id");
    let rowCount = 1;

    let productId = currentRow
        .find('select[name^="import_details"][name$="[product]"]')
        .val();
    if (!productId) {
        alert("Vui lòng chọn sản phẩm trước khi thêm biến thể!");
        return;
    }
    rowCount++;
    $.ajax({
        type: "get",
        url: `/api/product-variants/total/${productId}`,
        dataType: "json",
        success: function (response) {
            var totalProductVariant = response.total;
            var currentVariantCount = currentRow.find(
                ".product-variant-template"
            ).length;

            if (currentVariantCount >= totalProductVariant) {
                alert("Số lượng biến thể tối đa đã được thêm!");
                return;
            }

            let newVariantRow = currentRow
                .find("#product-variant-template")
                .clone();
            newVariantRow.removeAttr("id");
            newVariantRow.find("select, input").val("");

            let currentVariantIndex = currentVariantCount;
            newVariantRow.find("select, input").each(function () {
                let name = $(this).attr("name");
                if (name) {
                    $(this).attr(
                        "name",
                        name.replace("[0]", `[${currentVariantIndex}]`)
                    );
                }
                $(this).attr("data-row-id", currentVariantIndex);
            });

            newVariantRow.append(`
                    <div class="mb-3 col-md-3 text-end">
                        <button type="button" class="btn btn-danger btn-delete-variant">Xóa</button>
                    </div>
                `);

            // Changed this line to append after the last variant row
            currentRow
                .find(".product-variant-template")
                .last()
                .after(newVariantRow);
        },
    });
});
$(document).on("click", ".btn-delete-variant", function () {
    $(this).closest(".row").remove();
});

$(document).on(
    "change",
    'select[name^="import_details"][name$="[product]"]',
    function () {
        var productId = $(this).val();
        var currentRow = $(this).closest(".import-detail");
        var rowId = $(this).data("row-id");

        if (!productId) {
            currentRow.find(".variant-row").remove();
            currentRow
                .find('select[name$="[product_variant_id]"]')
                .empty()
                .append('<option value="">Chọn biến thể</option>');
            return;
        }

        $.ajax({
            type: "get",
            url: `/api/product-variants/${productId}`,
            dataType: "json",
            success: function (response) {
                currentRow.find(".variant-row").remove();
                let productVariantSelect = currentRow.find(
                    'select[name$="[product_variant_id]"]'
                );
                productVariantSelect
                    .empty()
                    .append('<option value="">Chọn biến thể</option>');

                response.forEach((variant) => {
                    productVariantSelect.append(
                        `<option value="${variant.id}">${variant.sku}</option>`
                    );
                });
            },
        });
    }
);
$(document).on(
    "focus",
    'select[name^="import_details"][name$="[product_variant_id]"]',
    function () {
        let selectedValue = $(this).val();
        let currentSelect = $(this);

        // Get all variant selects
        let allVariantSelects = $(
            'select[name^="import_details"][name$="[product_variant_id]"]'
        );

        // First enable all options
        allVariantSelects.find("option").prop("disabled", false);

        // Then disable selected values in other dropdowns
        allVariantSelects.each(function () {
            if (this !== currentSelect[0]) {
                let value = $(this).val();
                if (value) {
                    allVariantSelects
                        .find(`option[value="${value}"]`)
                        .prop("disabled", true);
                }
            }
        });

        // Keep the current selection enabled
        if (selectedValue) {
            currentSelect
                .find(`option[value="${selectedValue}"]`)
                .prop("disabled", true);
        }
    }
);
// Add this event handler after your existing code
$(document).on("click", "#add-all-row-variant", function () {
    let currentRow = $(this).closest(".import-detail");
    let productId = currentRow
        .find('select[name^="import_details"][name$="[product]"]')
        .val();

    if (!productId) {
        alert("Vui lòng chọn sản phẩm trước khi thêm biến thể!");
        return;
    }

    // Add confirmation dialog
    if (!confirm("Bạn có chắc chắn muốn thêm toàn bộ biến thể?")) {
        return;
    }

    // Add delete all button if it doesn't exist
    if (!currentRow.find("#delete-all-variants").length) {
        $(this).after(`
            <button type="button" id="delete-all-variants" class="btn btn-danger ms-2">
                Xóa toàn bộ biến thể
            </button>
        `);
    }

    // Get all product variants
    $.ajax({
        type: "get",
        url: `/api/product-variants/${productId}`,
        dataType: "json",
        success: function (response) {
            // Clear existing variants
            currentRow
                .find(
                    ".product-variant-template:not(#product-variant-template)"
                )
                .remove();

            // Add all variants
            response.forEach((variant, index) => {
                let newVariantRow = currentRow
                    .find("#product-variant-template")
                    .clone();
                newVariantRow.removeAttr("id");
                newVariantRow.find("select, input").val("");

                // Update names and indices
                newVariantRow.find("select, input").each(function () {
                    let name = $(this).attr("name");
                    if (name) {
                        $(this).attr("name", name.replace("[0]", `[${index}]`));
                    }
                    $(this).attr("data-row-id", index);
                });

                // Set the variant in dropdown
                newVariantRow
                    .find('select[name$="[product_variant_id]"]')
                    .empty()
                    .append(
                        `<option value="${variant.id}">${variant.sku}</option>`
                    );

                // Add delete button
                newVariantRow.append(`
                    <div class="mb-3 col-md-3 text-end">
                        <button type="button" class="btn btn-danger btn-delete-variant">Xóa</button>
                    </div>
                `);

                currentRow
                    .find("#product-variant-template")
                    .last()
                    .after(newVariantRow);
            });
        },
    });
});
// Add handler for delete all variants button
$(document).on("click", "#delete-all-variants", function () {
    let currentRow = $(this).closest(".import-detail");
    currentRow
        .find(".product-variant-template:not(#product-variant-template)")
        .remove();
    $(this).remove(); // Remove the delete all button itself
});
