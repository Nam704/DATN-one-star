import "../app.js";
$(document).ready(function () {
    // Khởi tạo các sự kiện
    initEvents();
    initFormSubmission();
});

// Biến toàn cục để lưu trữ các thuộc tính đã chọn
let selectedAttributes = [];

// Khởi tạo tất cả các sự kiện
function initEvents() {
    $("#attributeSelect").change(handleAttributeSelectChange);
    $(document).on("click", ".btn-remove-attribute", handleRemoveAttribute);
    $(document).on("click", ".remove-variant", handleRemoveVariant);
    $(document).on("click", ".attribute-title", toggleAttributeContent);
    $(document).on("click", ".variant-title", toggleVariantContent);
    $(document).on("click", ".create-value-attribute", showAttributeValueInput);
    $(document).on("click", ".btn-unsent-attribute", hideAttributeValueInput);
    $(document).on("click", ".add-all-value-attribute", handleAddAllValues);
    $(document).on(
        "click",
        ".delete-all-value-attribute",
        handleDeleteAllValues
    );
    $(document).on(
        "click",
        ".btn-sent-attribute",
        handleCreateNewAttributeValue
    );
    $(document).on("click", "#generate-variants", handleGenerateVariants);
    $(document).on("click", "#add-new-var", handleAddNewVariant);
    $(document).on("click", ".remove-value", removeAttributeValue);
    $(document).on("dblclick", ".attribute-value", removeAttributeValue);
    $(document).on("change", ".image-input", handleImageVariantPreview);
    $("#show_add_category").click(showAddCategorySection);
    $("#cancel_add_category").click(hideAddCategorySection);
    $("#confirm_add_category").click(addNewCategory);
    $("#show_add_brand").click(showAddBrandSection);
    $("#cancel_add_brand").click(hideAddBrandSection);
    $("#confirm_add_brand").click(addNewBrand);
    $("#add-new-attribute").click(showAddAttributeSection);
    $("#cancel_add_attribute").click(hideAddAttributeSection);
    $("#confirm_add_attribute").click(addNewAttribute);
    $("#productImage").change(handleProductImageSelection);
    $("#addAlbumImage").click(() => $("#albumImages").click());
    $("#albumImages").change(handleAlbumImageSelection);
}

// Xử lý submit form sản phẩm
function initFormSubmission() {
    $("#product-form").submit(function (e) {
        e.preventDefault();

        // Validate các trường bắt buộc
        const errors = [];
        if (!$("#name").val().trim()) {
            errors.push("Vui lòng nhập tên sản phẩm.");
        }
        if (!$("#category_select").val()) {
            errors.push("Vui lòng chọn danh mục.");
        }
        if (!$("#brand-select").val()) {
            errors.push("Vui lòng chọn thương hiệu.");
        }
        if ($("#productImage")[0].files.length === 0) {
            errors.push("Vui lòng chọn ảnh chính của sản phẩm.");
        }
        if (selectedImages.length === 0) {
            errors.push("Vui lòng chọn ít nhất một ảnh cho album.");
        }

        const variantRows = $(".variant-row");
        if (variantRows.length === 0) {
            errors.push("Sản phẩm phải có ít nhất 1 biến thể.");
        }
        if (variantRows.length > 50) {
            errors.push("Số lượng biến thể không được vượt quá 50.");
        }

        variantRows.each(function (index) {
            const sku = $(this)
                .find(`input[name='product_code_${index}']`)
                .val();
            const image = $(this).find(
                `input[name='image_variant_${index}']`
            )[0].files[0];
            if (!sku) {
                errors.push(`Vui lòng nhập mã SKU cho biến thể #${index + 1}.`);
            }
            if (!image) {
                errors.push(`Vui lòng chọn ảnh cho biến thể #${index + 1}.`);
            }
        });

        if (errors.length > 0) {
            GlobalUtils.showNotification(errors.join("\n"), {
                backgroundColor: "#ff4444",
            });
            return;
        }

        const formData = prepareProductData();
        axios
            .post(this.action, formData, {
                headers: { "Content-Type": "multipart/form-data" },
            })
            .then(() =>
                GlobalUtils.showNotification("Thêm sản phẩm thành công!", {
                    backgroundColor: "#00b09b",
                })
            )
            .catch(handleAjaxError);
    });
}

// Xử lý thay đổi lựa chọn thuộc tính
function handleAttributeSelectChange() {
    const attributeId = String($(this).val());
    if (!attributeId) return;

    const attributeName = $("#attributeSelect option:selected").text();
    if (selectedAttributes.includes(attributeId)) {
        GlobalUtils.showNotification("Bạn đã chọn thuộc tính này rồi!", {
            backgroundColor: "#ff4444",
        });
        return;
    }

    selectedAttributes.push(attributeId);
    addNewAttributeRow(attributeId, attributeName);
    $(this).val("");
}

// Thêm dòng thuộc tính mới vào giao diện
function addNewAttributeRow(id, name) {
    const newRow = `
        <div class="row attribute-row mb-3" data-id="${id}">
            <div class="col-12 row justify-content-between attribute-title" style="cursor: pointer;">
                <div class="col-4"><p>${name}</p></div>
                <div class="col-1">
                    <button type="button" class="btn btn-outline-danger btn-remove-attribute">Xóa</button>
                </div>
            </div>
            <hr class="border border-primary border-1 opacity-75">
            <div class="col-12 row attribute-content" style="display:none;">
                <div class="col-11 row">
                    <div class="attribute_value_current" id="attribute_value_current"></div>
                    <div class="col-12 row create-new-attribute_value mt-2" style="display:none;">
                        <div class="col-8">
                            <input type="text" class="form-control" name="attribute_value">
                        </div>
                        <div class="col-4 d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-primary me-2 btn-sent-attribute">Gửi</button>
                            <button type="button" class="btn btn-outline-danger btn-unsent-attribute">Hủy</button>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm w-100 mt-3">
                        <button type="button" class="btn btn-outline-primary add-all-value-attribute">Thêm toàn bộ</button>
                        <button type="button" class="btn btn-outline-primary delete-all-value-attribute">Xóa hết</button>
                        <button type="button" class="btn btn-outline-primary create-value-attribute">Tạo giá trị</button>
                    </div>
                </div>
            </div>
        </div>`;
    $("#new-attribute-row-container").append(newRow);
}

// Xóa thuộc tính khỏi giao diện và mảng
function handleRemoveAttribute() {
    const attributeRow = $(this).closest(".attribute-row");
    const attributeId = String(attributeRow.data("id"));
    attributeRow.remove();
    selectedAttributes = selectedAttributes.filter((id) => id !== attributeId);
}

// Hiển thị/ẩn nội dung thuộc tính
function toggleAttributeContent() {
    $(this).closest(".attribute-row").find(".attribute-content").toggle();
}

// Hiển thị/ẩn input giá trị thuộc tính
function showAttributeValueInput() {
    $(this)
        .closest(".attribute-content")
        .find(".create-new-attribute_value")
        .show();
}

function hideAttributeValueInput() {
    $(this)
        .closest(".attribute-content")
        .find(".create-new-attribute_value")
        .hide();
}

// Lấy và hiển thị tất cả giá trị thuộc tính từ server
function handleAddAllValues() {
    const attributeRow = $(this).closest(".attribute-row");
    const attributeId = attributeRow.data("id");
    axios
        .get(
            `http://127.0.0.1:8000/api/admin/attributes/get-by-id/${attributeId}`
        )
        .then(({ data }) => {
            if (data.status === "success") {
                const container = attributeRow
                    .find("#attribute_value_current")
                    .empty();
                Object.entries(data.data.values).forEach(([key, value]) => {
                    container.append(`
                        <span class="badge bg-info m-1 p-2 attribute-value" data-key="${key}">
                            ${value} <span class="ms-2 text-bg-info remove-value" style="cursor: pointer;">×</span>
                        </span>`);
                });
            } else {
                GlobalUtils.showNotification(
                    "Không thể lấy dữ liệu thuộc tính.",
                    { backgroundColor: "#ff4444" }
                );
            }
        })
        .catch(handleAjaxError);
}

// Xóa giá trị thuộc tính
function removeAttributeValue() {
    $(this).closest(".attribute-value").remove();
}

// Xóa toàn bộ giá trị thuộc tính
function handleDeleteAllValues() {
    $(this).closest(".attribute-row").find("#attribute_value_current").empty();
}

// Tạo giá trị thuộc tính mới
function handleCreateNewAttributeValue() {
    const inputContainer = $(this).closest(".create-new-attribute_value");
    const inputValue = inputContainer
        .find("input[name='attribute_value']")
        .val();
    const attributeRow = $(this).closest(".attribute-row");
    const attributeId = attributeRow.data("id");

    if (!inputValue) {
        GlobalUtils.showNotification("Vui lòng nhập giá trị thuộc tính!", {
            backgroundColor: "#ff4444",
        });
        return;
    }

    axios
        .post(
            `http://127.0.0.1:8000/api/admin/attributes/creat-values/${attributeId}`,
            { value: inputValue }
        )
        .then(({ data }) => {
            const container = attributeRow.find("#attribute_value_current");
            container.append(`
                <span class="badge bg-info m-1 p-2 attribute-value" data-key="${data.data.id}">
                    ${data.data.value} <span class="ms-2 text-bg-info remove-value" style="cursor: pointer;">×</span>
                </span>`);
            inputContainer.hide();
            GlobalUtils.showNotification(
                "Thêm giá trị thuộc tính thành công!",
                { backgroundColor: "#00b09b" }
            );
        })
        .catch(handleAjaxError);
}

// Tạo và hiển thị các biến thể tự động
function handleGenerateVariants() {
    const variants = [];
    $(".attribute-row").each(function () {
        const attributeId = $(this).data("id");
        const attributeName = $(this).find(".attribute-title p").text();
        const values = [];
        $(this)
            .find(".attribute-value")
            .each(function () {
                values.push({
                    id_value: $(this).data("key"),
                    value: $(this).text().trim().replace("×", "").trim(),
                });
            });
        if (values.length) {
            variants.push({
                attribute_id: attributeId,
                attribute_name: attributeName,
                values,
            });
        }
    });

    const generatedVariants = generateCombinations(variants);
    // Chỉ render tối đa 50 biến thể
    renderVariantsLayout(generatedVariants.slice(0, 50));
}

// Hiển thị form thêm biến thể thủ công
function showManualVariantForm() {
    if ($(".variant-row").length >= 50) {
        GlobalUtils.showNotification(
            "Không thể thêm biến thể, đã đạt tối đa 50 biến thể.",
            { backgroundColor: "#ff4444" }
        );
        return;
    }

    const modalHtml = `
        <div class="modal fade" id="manualVariantModal" tabindex="-1" aria-labelledby="manualVariantModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="manualVariantModalLabel">Thêm biến thể thủ công</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="manual-variant-form">
                            ${selectedAttributes
                                .map(
                                    (attrId, index) => `
                                <div class="mb-3">
                                    <label class="form-label">Thuộc tính ${$(
                                        `.attribute-row[data-id="${attrId}"] .attribute-title p`
                                    ).text()}</label>
                                    <select class="form-control manual-variant-value" data-attr-id="${attrId}">
                                        <option value="">Chọn giá trị</option>
                                        ${$(
                                            `.attribute-row[data-id="${attrId}"] .attribute-value`
                                        )
                                            .map(
                                                (i, el) => `
                                            <option value="${$(el).data(
                                                "key"
                                            )}" data-value="${$(el)
                                                    .text()
                                                    .trim()
                                                    .replace("×", "")
                                                    .trim()}">
                                                ${$(el)
                                                    .text()
                                                    .trim()
                                                    .replace("×", "")
                                                    .trim()}
                                            </option>
                                        `
                                            )
                                            .get()
                                            .join("")}
                                    </select>
                                </div>
                            `
                                )
                                .join("")}
                            <div class="mb-3">
                                <label class="form-label">Mã sản phẩm (SKU)</label>
                                <input type="text" class="form-control" id="manual-variant-sku" placeholder="Nhập mã sản phẩm">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ảnh biến thể</label>
                                <input type="file" class="form-control" id="manual-variant-image" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary" id="confirm-manual-variant">Thêm biến thể</button>
                    </div>
                </div>
            </div>
        </div>`;

    $("body").append(modalHtml);
    const modal = new bootstrap.Modal(
        document.getElementById("manualVariantModal")
    );
    modal.show();

    $("#confirm-manual-variant").click(function () {
        const variantData = [];
        let hasError = false;
        $(".manual-variant-value").each(function () {
            const attrId = $(this).data("attr-id");
            const valueId = $(this).val();
            const valueText = $(this).find("option:selected").data("value");
            if (!valueId) {
                hasError = true;
                GlobalUtils.showNotification(
                    `Vui lòng chọn giá trị cho thuộc tính ${$(
                        `.attribute-row[data-id="${attrId}"] .attribute-title p`
                    ).text()}.`,
                    { backgroundColor: "#ff4444" }
                );
                return false;
            }
            variantData.push({
                attribute_id: attrId,
                attribute_name: $(
                    `.attribute-row[data-id="${attrId}"] .attribute-title p`
                ).text(),
                value: { id_value: valueId, value: valueText },
            });
        });

        const sku = $("#manual-variant-sku").val();
        const image = $("#manual-variant-image")[0].files[0];
        if (!sku) {
            hasError = true;
            GlobalUtils.showNotification("Vui lòng nhập mã SKU cho biến thể.", {
                backgroundColor: "#ff4444",
            });
        }
        if (!image) {
            hasError = true;
            GlobalUtils.showNotification("Vui lòng chọn ảnh cho biến thể.", {
                backgroundColor: "#ff4444",
            });
        }

        if (hasError) return;

        // Thêm SKU và ảnh vào dữ liệu biến thể
        const fullVariantData = {
            attributes: variantData,
            sku: sku,
            image: image,
        };

        // Render biến thể với dữ liệu đầy đủ
        renderVariantsLayout([fullVariantData], true);
        modal.hide();
        $("#manualVariantModal").remove();
    });

    $("#manualVariantModal").on("hidden.bs.modal", function () {
        $(this).remove();
    });
}

// Xử lý thêm biến thể thủ công
function handleAddNewVariant() {
    if (selectedAttributes.length === 0) {
        GlobalUtils.showNotification(
            "Vui lòng chọn ít nhất một thuộc tính trước khi thêm biến thể.",
            { backgroundColor: "#ff4444" }
        );
        return;
    }
    showManualVariantForm();
}

// Tính tổng số tổ hợp biến thể
function calculateTotalCombinations(variants) {
    return variants.reduce((total, attr) => total * attr.values.length, 1);
}

// Hiển thị layout biến thể
function renderVariantsLayout(variants, isManual = false) {
    const container = $("#container-variations");
    if (!isManual) {
        container.empty();
    }
    if (!variants.length) {
        if (!isManual) {
            container.append("<p>Không có biến thể nào được tạo.</p>");
        }
        return;
    }

    const currentVariantCount = $(".variant-row").length;
    variants.forEach((variant, index) => {
        if (currentVariantCount + index >= 50) {
            GlobalUtils.showNotification("Đã đạt tối đa 50 biến thể.", {
                backgroundColor: "#ff4444",
            });
            return;
        }
        // Kiểm tra xem variant là mảng (tự động) hay đối tượng (thủ công)
        const attributes = Array.isArray(variant)
            ? variant
            : variant.attributes;
        const variantDetails = attributes
            .map((v) => `${v.attribute_name}: ${v.value.value}`)
            .join(", ");
        const variantIndex = currentVariantCount + index;
        const variantRow = `
            <div class="variant-row border rounded p-1 mb-1" data-id="${variantIndex}">
                <div class="row mb-1 variant-title">
                    <div class="col-12 d-flex justify-content-between">
                        <h6 class="mb-0">#${
                            variantIndex + 1
                        } - ${variantDetails}</h6>
                        <div>
                            <button type="button" class="btn btn-outline-danger remove-variant me-3">Xóa</button>
                            <button type="button" class="btn btn-outline-primary edit-variant">Sửa</button>
                        </div>
                    </div>
                </div>
                <div class="row mb-1 variant-content" style="display:none;">
                    <input type="hidden" name="variant_attribute_values_${variantIndex}" value='${JSON.stringify(
            attributes
        )}' />
                    <div class="col-md-2 text-center">
                        <div class="image-placeholder border d-flex align-items-center justify-content-center image-variant" style="height: 100px; position: relative;">
                            <input type="file" class="form-control image-input" name="image_variant_${variantIndex}" accept="image/*" style="opacity: 0; position: absolute; width: 100%; height: 100%;">
                            ${
                                !Array.isArray(variant) && variant.image
                                    ? `
                                <img src="${URL.createObjectURL(
                                    variant.image
                                )}" alt="Preview" class="image-preview" style="max-height: 100%; max-width: 100%; display: block;">
                            `
                                    : '<img src="" alt="Preview" class="image-preview" style="max-height: 100%; max-width: 100%; display: none;">'
                            }
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label class="form-label">Mã sản phẩm(SKU)</label>
                                <input type="text" class="form-control" name="product_code_${variantIndex}" placeholder="Nhập mã sản phẩm" value="${
            !Array.isArray(variant) ? variant.sku || "" : ""
        }">
                            </div>
                            <div class="" style="display:none;">
                                <label class="form-label">Giá ($)</label>
                                <input type="number" class="form-control" name="product_price_${variantIndex}" placeholder="Nhập giá sản phẩm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        container.append(variantRow);
    });
}

// Xử lý ảnh xem trước của biến thể
function handleImageVariantPreview(event) {
    const file = event.target.files[0];
    if (!file || !file.type.startsWith("image/")) {
        GlobalUtils.showNotification("Vui lòng chọn một file ảnh hợp lệ!", {
            backgroundColor: "#ff4444",
        });
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        $(event.target)
            .siblings(".image-preview")
            .attr("src", e.target.result)
            .show();
    };
    reader.readAsDataURL(file);
}

// Tạo tổ hợp biến thể
function generateCombinations(variants) {
    if (!variants.length) return [];

    function combine(index, current) {
        if (index === variants.length) return [current];
        const result = [];
        const attribute = variants[index];
        attribute.values.forEach((value) => {
            const newCombination = [
                ...current,
                {
                    attribute_id: attribute.attribute_id,
                    attribute_name: attribute.attribute_name,
                    value,
                },
            ];
            result.push(...combine(index + 1, newCombination));
        });
        return result;
    }

    return combine(0, []);
}

// Hiển thị/ẩn nội dung biến thể
function toggleVariantContent() {
    $(this).closest(".variant-row").find(".variant-content").toggle();
}

// Xóa biến thể
function handleRemoveVariant() {
    $(this).closest(".variant-row").remove();
}

// Chuẩn bị dữ liệu sản phẩm trước khi gửi
function prepareProductData() {
    const formData = new FormData(document.querySelector("#product-form"));
    const quillContent = quill ? quill.root.innerHTML : $("#description").val();
    formData.append("description", quillContent || "");
    formData.append("name", formData.get("name").trim() || "");
    formData.append("id_category", formData.get("id_category"));
    formData.append("id_brand", formData.get("id_brand"));

    $(".variant-row").each((index, row) => {
        const productCode = $(row)
            .find(`input[name='product_code_${index}']`)
            .val();
        const productPrice = $(row)
            .find(`input[name='product_price_${index}']`)
            .val();
        const attributeValues = JSON.parse(
            $(row).find(`input[name='variant_attribute_values_${index}']`).val()
        );
        formData.append(`variants[${index}][code]`, productCode);
        formData.append(`variants[${index}][price]`, productPrice);
        formData.append(
            `variants[${index}][attributes]`,
            JSON.stringify(attributeValues)
        );
        const imageInput = $(row).find(
            `input[name='image_variant_${index}']`
        )[0].files[0];
        if (imageInput)
            formData.append(`variants[${index}][image]`, imageInput);
    });

    return formData;
}

// Hiển thị phần thêm danh mục
function showAddCategorySection() {
    $("#add-category-section").show();
    $("#show_add_category").hide();
}

// Ẩn phần thêm danh mục
function hideAddCategorySection() {
    $("#add-category-section").hide();
    $("#show_add_category").show();
}

// Thêm danh mục mới
function addNewCategory() {
    const name = $("#new_category_name").val();
    const parentId = $("#id_parent").val() || 0;
    if (!name) {
        GlobalUtils.showNotification("Vui lòng nhập tên danh mục mới!", {
            backgroundColor: "#ff4444",
        });
        return;
    }

    axios
        .post("http://127.0.0.1:8000/api/admin/categories", {
            name,
            id_parent: parentId,
        })
        .then(({ data }) => {
            if (data.status === "success") {
                GlobalUtils.showNotification("Thêm danh mục thành công!", {
                    backgroundColor: "#00b09b",
                });
                hideAddCategorySection();
                updateCategoryList(data.data);
            } else {
                GlobalUtils.showNotification(
                    data.message || "Thêm danh mục thất bại!",
                    { backgroundColor: "#ff4444" }
                );
            }
        })
        .catch(handleAjaxError);
}

// Cập nhật danh sách danh mục
function updateCategoryList(category) {
    const option = $("<option>", {
        value: category.id,
        text: category.id_parent != 0 ? `-- ${category.name}` : category.name,
    }).prop("selected", true);

    if (category.id_parent != 0) {
        $(`#category_select option[value='${category.id_parent}']`).after(
            option
        );
    } else {
        $("#category_select").append(option);
        $("#id_parent").append(
            $("<option>", { value: category.id, text: category.name })
        );
    }
}

// Hiển thị phần thêm thương hiệu
function showAddBrandSection() {
    $("#add-brand-section").show();
    $("#show_add_brand").hide();
}

// Ẩn phần thêm thương hiệu
function hideAddBrandSection() {
    $("#add-brand-section").hide();
    $("#show_add_brand").show();
}

// Thêm thương hiệu mới
function addNewBrand() {
    const name = $("#new_brand_name").val();
    if (!name) {
        GlobalUtils.showNotification("Vui lòng nhập tên thương hiệu mới!", {
            backgroundColor: "#ff4444",
        });
        return;
    }

    axios
        .post("http://127.0.0.1:8000/api/admin/brands", { name })
        .then(({ data }) => {
            if (data.status === "success") {
                GlobalUtils.showNotification("Thêm thương hiệu thành công!", {
                    backgroundColor: "#00b09b",
                });
                hideAddBrandSection();
                $("#brand-select").append(
                    $("<option>", {
                        value: data.data.id,
                        text: data.data.name,
                    }).prop("selected", true)
                );
            } else {
                GlobalUtils.showNotification(
                    data.message || "Thêm thương hiệu thất bại!",
                    { backgroundColor: "#ff4444" }
                );
            }
        })
        .catch(handleAjaxError);
}

// Hiển thị phần thêm thuộc tính
function showAddAttributeSection() {
    $("#add-attribute-section").show();
}

// Ẩn phần thêm thuộc tính
function hideAddAttributeSection() {
    $("#add-attribute-section").hide();
}

// Thêm thuộc tính mới
function addNewAttribute() {
    const name = $("#new_attribute_name").val();
    if (!name) {
        GlobalUtils.showNotification("Vui lòng nhập tên thuộc tính mới!", {
            backgroundColor: "#ff4444",
        });
        return;
    }

    axios
        .post("http://127.0.0.1:8000/api/admin/attributes", { name })
        .then(({ data }) => {
            if (data.status === "success") {
                GlobalUtils.showNotification("Thêm thuộc tính thành công!", {
                    backgroundColor: "#00b09b",
                });
                hideAddAttributeSection();
                $("#attributeSelect").append(
                    $("<option>", {
                        value: data.data.id,
                        text: data.data.name,
                    }).prop("selected", true)
                );
            } else {
                GlobalUtils.showNotification(
                    data.message || "Thêm thuộc tính thất bại!",
                    { backgroundColor: "#ff4444" }
                );
            }
        })
        .catch(handleAjaxError);
}

// Xử lý chọn ảnh sản phẩm
function handleProductImageSelection(event) {
    const file = event.target.files[0];
    if (!file || !file.type.startsWith("image/")) {
        GlobalUtils.showNotification("Vui lòng chọn một file ảnh hợp lệ!", {
            backgroundColor: "#ff4444",
        });
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        $("#imagePreview").html(`
            <div class="position-relative d-inline-block" id="imageContainer" style="max-width: 200px;">
                <img src="${e.target.result}" alt="Ảnh sản phẩm" class="img-fluid rounded" style="width: 100%; height: auto;" />
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-image" style="z-index: 10;">X</button>
            </div>`);
        $(".remove-image").click(() => {
            $("#imageContainer").remove();
            $("#productImage").val("");
        });
    };
    reader.readAsDataURL(file);
}

// Xử lý chọn ảnh album
let selectedImages = [];
function handleAlbumImageSelection() {
    const files = $("#albumImages")[0].files;
    let added = 0;
    for (let i = 0; i < files.length && selectedImages.length < 4; i++) {
        selectedImages.push(files[i]);
        added++;
    }
    if (files.length > 4 - selectedImages.length) {
        GlobalUtils.showNotification("Bạn chỉ được chọn tối đa 4 ảnh.", {
            backgroundColor: "#ff4444",
        });
    }
    updateAlbumPreview();
}

// Cập nhật xem trước ảnh album
function updateAlbumPreview() {
    const albumPreview = $("#albumPreview").empty();
    selectedImages.forEach((image, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const colDiv = $("<div>", { class: "col-3" }).html(`
                <div class="position-relative">
                    <img src="${e.target.result}" class="img-fluid rounded shadow" alt="Album Image">
                    <button type="button" class="btn btn-danger btn-sm remove-image-btn w-100" data-index="${index}">X</button>
                </div>`);
            colDiv.find(".remove-image-btn").click(() => {
                selectedImages.splice(index, 1);
                updateAlbumPreview();
            });
            albumPreview.append(colDiv);
        };
        reader.readAsDataURL(image);
    });
}

// Xử lý lỗi AJAX
function handleAjaxError(err) {
    console.error(err.response?.data || err);
    let message = "Có lỗi xảy ra, vui lòng thử lại!";
    if (err.response && err.response.data) {
        if (err.response.data.message) {
            message = err.response.data.message;
        } else if (err.response.data.errors) {
            message = Object.values(err.response.data.errors).flat().join("\n");
        }
    }
    GlobalUtils.showNotification(message, { backgroundColor: "#ff4444" });
}
