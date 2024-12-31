$(document).ready(function () {
    // Lấy ngày hôm nay
    const today = new Date().toLocaleDateString("en-CA");

    // Gán giá trị cho input ngày nhập
    $("#import-date").val(today);

    // Hàm cập nhật import_name
    function updateImportName() {
        const supplierName = $("#supplier-name")
            .find("option:selected")
            .text()
            .trim(); // Loại bỏ khoảng trắng thừa
        const importDate = $("#import-date").val().trim(); // Loại bỏ khoảng trắng thừa

        if (supplierName && importDate) {
            const importName = supplierName + "_" + importDate;
            $("#import-name").val(importName);
        }
    }

    // Lắng nghe sự kiện thay đổi trên input supplier và import date để cập nhật import_name
    $("#supplier-name").on("input", updateImportName);
    $("#import-date").on("change", updateImportName);
    $(document).on(
        "input",
        '[name^="variant-product"][name$="][quantity]"], [name^="variant-product"][name$="][price_per_unit]"]',
        function () {
            var variantRow = $(this).closest(".product-variant");
            var productRow = $(this).closest(".product-row");

            // Calculate individual variant total
            var quantity =
                parseFloat(variantRow.find('[name$="[quantity]"]').val()) || 0;
            var pricePerUnit =
                parseFloat(
                    variantRow.find('[name$="[price_per_unit]"]').val()
                ) || 0;
            var totalPrice = quantity * pricePerUnit;

            // Update variant total price
            variantRow
                .find('[name^="variant-product"][name$="[total_price]"]')
                .val(totalPrice.toFixed(2));

            // Sum up all variant totals in this product
            var productTotal = 0;
            productRow
                .find('[name^="variant-product"][name$="[total_price]"]')
                .each(function () {
                    productTotal += parseFloat($(this).val()) || 0;
                });

            // Update product total price field
            productRow
                .find(`[name^="product"][name$="[total_price]"]`)
                .val(productTotal.toFixed(2));
            let totalAmount = 0;
            $('[name^="product"][name$="[total_price]"]').each(function () {
                totalAmount += parseFloat($(this).val()) || 0;
            });

            // Update the total-amount field
            $("#total-amount").val(totalAmount.toFixed(2));
        }
    );

    // Gọi totalProduct và xử lý Promise
    totalProduct()
        .then((result) => {
            console.log(result); // In ra products
            let rowId = 0;
            const products = result;
            const maxProductRow = result.length;
            // hóa 1 hàng sản phẩm
            $(document).on("click", ".btn-delete-product", function (e) {
                $(this).closest(".product-row").remove();
                recalculateAllTotals();
            });

            // thêm 1 hàng sản phẩm
            $("#add-row-btn").click(function () {
                const currentRowCount = $(".product-row").length; // Đếm số hàng hiện tại trong DOM
                if (currentRowCount < maxProductRow) {
                    const newRow = createProductRow(
                        currentRowCount + 1,
                        products
                    );
                    $(".product-rows-container").append(newRow);
                } else {
                    alert("Sản phẩm được thêm tối đa");
                }
            });
            // kiểm tra số lượng biến thể trước khi submit
            $(document).on("click", "#btn-submit", function (e) {
                let isValid = true;
                let errorMessage = "";

                // e.preventDefault();

                // Lặp qua tất cả các hàng sản phẩm
                $(".product-row").each(function () {
                    const productRow = $(this);
                    const rowVariantContainer = productRow
                        .find(".row-variant")
                        .children();
                    const currentRowId = productRow.data("row-id");
                    console.log(rowVariantContainer.length);
                    // Kiểm tra nếu không có biến thể nào được chọn trong hàng này
                    const selectedVariants = productRow
                        .find(
                            `select[name^="variant-product[${currentRowId}][product_variant_id]"]`
                        )
                        .map(function () {
                            return $(this).val();
                        })
                        .get();

                    // Nếu không có bất kỳ biến thể nào được chọn, báo lỗi và ngừng submit
                    if (
                        selectedVariants.every((variantId) => !variantId) ||
                        rowVariantContainer.length < 1
                    ) {
                        isValid = false;
                        errorMessage += `Sản phẩm với thứ ${currentRowId} phải có ít nhất 1 biến thể được chọn.\n`;
                    }
                });

                // Nếu có lỗi, hiển thị thông báo và không submit
                if (!isValid) {
                    alert(errorMessage);
                    return;
                }
            });
            // xóa biến thể khi chọn sản phẩm khác
            $(document).on("change", `select[name^="product"]`, function () {
                const productRow = $(this).closest(".product-row");
                productRow.find(".row-variant").empty();
            });
            // kiểm tra tùy chọn sản phẩm
            $(document).on("focus", `select[name^="product"]`, function () {
                disableSelectedOptions(`select[name^="product"]`);
            });
            // kiểm tra tùy chọn biến thể
            $(document).on(
                "focus",
                `select[name^="variant-product"]`,
                function () {
                    disableSelectedOptions(`select[name^="variant-product"]`);
                }
            );
            // Xóa toàn bộ biến thể
            $(document).on("click", ".btn-delete-all", function () {
                var check = confirm("Xóa toàn bộ biến thể");
                if (check) {
                    const productRow = $(this).closest(".product-row");
                    productRow.find(".row-variant").empty();
                    alert(
                        "Tất cả biến thể đã được xóa cho hàng sản phẩm:",
                        productRow.data("row-id")
                    );
                    recalculateAllTotals();
                }
            });
            // Thêm biến thể
            $(document).on("click", ".add-row-variant", function () {
                const productRow = $(this).closest(".product-row");
                const rowVariantContainer = productRow.find(".row-variant");
                const currentRowId = productRow.data("row-id");

                const productSelected = productRow
                    .find(`select[name^="product"]`)
                    .val();

                if (!productSelected) {
                    alert("Vui lòng chọn sản phẩm trước khi thêm biến thể.");
                    return;
                }

                // Gọi API để lấy số lượng biến thể tối đa cho sản phẩm được chọn
                $.ajax({
                    url: `/api/product-variants/${productSelected}`, // API trả về maxVariants
                    method: "GET",
                    success: function (response) {
                        const maxVariants = response.length || 0;
                        var variants = response;
                        const variantCount =
                            rowVariantContainer.children().length;
                        if (variantCount >= maxVariants) {
                            alert(
                                `Sản phẩm này chỉ cho phép tối đa ${maxVariants} biến thể.`
                            );
                            return;
                        }
                        // Thêm biến thể mới
                        rowVariantContainer.append(
                            createRowVariant(currentRowId, variants)
                        );
                        recalculateAllTotals();
                        // Lắng nghe sự kiện xóa biến thể
                        productRow
                            .find(".btn-delete-variant")
                            .off("click")
                            .on("click", function () {
                                $(this).closest(".product-variant").remove();
                                recalculateAllTotals();
                            });
                    },
                    error: function (error) {
                        console.log("Lỗi khi lấy maxVariants:", error);
                    },
                });
            });
            //thêm nhiều biến thể
            $(document).on("click", ".btn-add-all", function () {
                var check = confirm(
                    "Thêm toàn bộ biến thể sẽ xóa các biến thể trước đó!"
                );
                if (check) {
                    const productRow = $(this).closest(".product-row");
                    productRow.find(".row-variant").empty();
                    const rowVariantContainer = productRow.find(".row-variant");
                    const currentRowId = productRow.data("row-id");

                    const productSelected = productRow
                        .find(`select[name^="product"]`)
                        .val();

                    if (!productSelected) {
                        alert(
                            "Vui lòng chọn sản phẩm trước khi thêm biến thể."
                        );
                        return;
                    }

                    // Gọi API để lấy tất cả biến thể cho sản phẩm được chọn
                    $.ajax({
                        url: `/api/product-variants/${productSelected}`, // API trả về tất cả các biến thể
                        method: "GET",
                        success: function (response) {
                            const variants = response; // Lấy danh sách các biến thể
                            const maxVariants = variants.length;

                            const currentVariantCount =
                                rowVariantContainer.children().length;

                            if (currentVariantCount >= maxVariants) {
                                alert(`Sản phẩm này đã có đủ biến thể.`);
                                return;
                            }

                            // Lấy các ID của biến thể đã được chọn
                            const selectedVariantIds = rowVariantContainer
                                .find(".product-variant")
                                .map(function () {
                                    return $(this).data("variant-id");
                                })
                                .get();

                            // Chỉ thêm các biến thể chưa được chọn
                            variants.forEach((variant) => {
                                if (!selectedVariantIds.includes(variant.id)) {
                                    rowVariantContainer.append(
                                        createRowVariant(
                                            currentRowId,
                                            variants,
                                            variant.id
                                        )
                                    );
                                }
                            });
                            recalculateAllTotals();
                            // Lắng nghe sự kiện xóa biến thể
                            productRow
                                .find(".btn-delete-variant")
                                .off("click")
                                .on("click", function () {
                                    $(this)
                                        .closest(".product-variant")
                                        .remove();
                                    recalculateAllTotals();
                                });
                        },
                        error: function (error) {
                            console.log("Lỗi khi lấy biến thể:", error);
                        },
                    });
                }
            });
        })
        .catch((error) => {
            console.log("Lỗi:", error);
        });
});
function disableSelectedOptions(selectSelector) {
    let allSelects = $(selectSelector);
    // Enable tất cả các tùy chọn trước
    allSelects.find("option").prop("disabled", false);
    // Lặp qua tất cả các select và disable giá trị đã được chọn
    allSelects.each(function () {
        let selectedValue = $(this).val();
        if (selectedValue) {
            allSelects
                .not(this) // Không áp dụng với select hiện tại
                .find(`option[value="${selectedValue}"]`)
                .prop("disabled", true);
        }
    });
}

async function totalProduct() {
    try {
        return new Promise((resolve, reject) => {
            $.getJSON("/api/products/list", function (data) {
                resolve(data);
            }).fail(function (error) {
                reject(error);
            });
        });
    } catch (error) {
        console.log("Lỗi:", error);
    }
}

function createSelectField(name, label, options = [], required = true) {
    let optionsHtml = options
        .map((option) => `<option value="${option.id}">${option.name}</option>`)
        .join("");

    return `
        <div class="mb-3 col-md-5">
        <div class="d-flex align-items-center justify-content-between">
        <label for="${name}" class="form-label">${label}</label>
        <button type="button" class="btn btn-primary btn-add-all">Add all variant</button>
        <button type="button" class="btn btn-danger btn-delete-all">Delete all variant</button>
    </div>
            <select name="${name}" class="form-select" ${
        required ? "required" : ""
    }>
                <option value="">Select ${label}</option>
                ${optionsHtml}
            </select>
        </div>
    `;
}

function createField(
    name,
    label,
    type = "text",
    step = "",
    required = true,
    value = "",
    max = "",
    readonly = false
) {
    return `
        <div class="mb-3 col-md-5 mt-auto">
            <label for="${name}" class="form-label">${label}</label>
            <input type="${type}" name="${name}" class="form-control" ${
        step ? `step="${step}"` : ""
    } ${required ? "required" : ""} value="${value}" min="0" max="${max}" ${
        readonly ? "readonly" : ""
    }>
        </div>
    `;
}

function createProductRow(rowId = 0, products = []) {
    return `
        <div class="row product-row d-flex" data-row-id="${rowId}">
            ${createSelectField(`product[${rowId}]`, "Product", products)}
            ${createField(
                `product[${rowId}][total_price]`,
                "Total Price Product",
                "number",
                "0.01",
                true,
                "",
                "9999999999",
                true
            )}
            <div class="mb-3 col-md-2 text-end mt-auto">
            <button type="button" class="btn btn-danger w-100 btn-delete-product" data-row-id="${rowId}">Delete</button>
            </div>
            <div class="detail-variant"><div class="row-variant"></div> </div>
            <div class="mb-3 col-md-12 d-flex align-items-center justify-content-between">
            <button type="button"  class="btn btn-primary add-row-variant ">Thêm một biến thể </button>
            </div>
        </div>
        
    `;
}

// hàm tạo trường nhập liệu product_variant
function createFieldVariant(
    name,
    label,
    type = "text",
    step = "",
    required = true,
    value = "",
    max = "",
    readonly = false // Tham số mới để kiểm tra readonly
) {
    return `
    <div class="mb-3 col-md-4">
        <label for="${name}" class="form-label">${label}</label>
        <input type="${type}" name="${name}" class="form-control" ${
        step ? `step="${step}"` : ""
    } ${required ? "required" : ""} value="${value}" min="0" max="${max}" ${
        readonly ? "readonly" : ""
    }>
    </div>
    `;
}

// Hàm tạo phần chọn sản phẩm
function createSelectFieldVariant(
    name,
    label,
    options = [],
    required = true,
    selectedVariantId = null
) {
    let optionsHtml = options
        .map((option) => {
            const selected = option.id === selectedVariantId ? "selected" : "";
            return `<option value="${option.id}" ${selected}>${option.sku}</option>`;
        })
        .join("");
    return `
    <div class="mb-3 col-md-4">
        <label for="${name}" class="form-label">${label}</label>
        <select name="${name}" class="form-control" ${
        required ? "required" : ""
    }>
    <option value="">Select variant</option>
            ${optionsHtml}
        </select>
    </div>
`;
}

// Hàm tạo dòng nhập liệu với rowId
function createRowVariant(rowId = 0, options = [], selectedVariantId = null) {
    return `
    <div class="row product-variant" data-row-id="${rowId}" data-variant-id="${selectedVariantId}">
        ${createSelectFieldVariant(
            `variant-product[${rowId}][product_variant_id]`,
            "Product Variant",
            options,
            true,
            selectedVariantId
        )}
        ${createFieldVariant(
            `variant-product[${rowId}][quantity]`,
            "Quantity",
            "number",
            "1",
            true,
            "",
            "9999"
        )}
        ${createFieldVariant(
            `variant-product[${rowId}][price_per_unit]`,
            "Price per Unit",
            "number",
            "0.01",
            true,
            "",
            "900000000"
        )}
        ${createFieldVariant(
            `variant-product[${rowId}][expected_price]`,
            "Expected Price",
            "number",
            "0.01",
            true,
            "",
            "900000000"
        )}
        ${createFieldVariant(
            `variant-product[${rowId}][total_price]`,
            "Total Price",
            "number",
            "0.01",
            true,
            "",
            "9999999999",
            true
        )}
        <div class="mb-3 mt-auto col-md-4 text-end">
            <button type="button" class="btn btn-danger btn-delete-variant  w-100" data-row-id="${rowId}">Delete</button>
        </div>
        <hr class="border border-primary border-3 opacity-75">
    </div>
`;
}
function recalculateAllTotals() {
    let totalAmount = 0;

    // Calculate totals for each product row
    $(".product-row").each(function () {
        let productRow = $(this);
        let productTotal = 0;

        // Sum variant totals within this product
        productRow
            .find('[name^="variant-product"][name$="[total_price]"]')
            .each(function () {
                productTotal += parseFloat($(this).val()) || 0;
            });

        // Update product total
        productRow
            .find(`[name^="product"][name$="[total_price]"]`)
            .val(productTotal.toFixed(2));
        totalAmount += productTotal;
    });

    // Update import total amount
    $("#total-amount").val(totalAmount.toFixed(2));
}
