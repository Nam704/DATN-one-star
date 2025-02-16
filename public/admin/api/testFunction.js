$(document).ready(function () {
    var selected = [];

    function initEvents() {
        $("#attributeSelect").change(handleAttributeSelectChange);
        $(document).on("click", ".btn-remove-attribute", handleRemoveAttribute);
        $(document).on("click", ".remove-variant", handleRemoveVariant);
        $(document).on("click", ".attribute-title", toggleAttributeContent);
        $(document).on("click", ".variant-title", tonggleVariant);
        $(document).on(
            "click",
            ".create-value-attribute",
            showAttributeValueInput
        );
        $(document).on(
            "click",
            ".btn-unsent-attribute",
            hideAttributeValueInput
        );
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
    }
    function handleAttributeSelectChange() {
        var attributeId = $(this).val();
        if (!attributeId) return;

        var attributeName = $("#attributeSelect option:selected").text();
        if (selected.includes(attributeId)) {
            alert("Bạn đã chọn thuộc tính này rồi!");
            return;
        }

        selected.push(attributeId);
        addNewAttributeRow(attributeId, attributeName);
    }

    function addNewAttributeRow(attributeId, attributeName) {
        var newRow = `
            <div class="row attribute-row mb-3" data-id="${attributeId}">
                <div class="col-12 row justify-content-between attribute-title" style="cursor: pointer;">
                    <div class="col-4"><p>${attributeName}</p></div>
                    <div class="col-1">
                        <button type="button" class="btn btn-outline-danger btn-remove-attribute">Xóa</button>
                    </div>
                </div>
                <hr class="border border-primary border-1 opacity-75">
                <div class="col-12 row attribute-content" style="display:none;">
                    <div class="col-11 row">
                        <!-- Chỗ này sẽ chứa các giá trị thuộc tính -->
                        <div class="attribute_value_current" id="attribute_value_current"></div>
    
                        <div class="col-12 row create-new-attribute_value mt-2" style="display:none;">
                            <div class="col-8">
                                <input type="text" class="form-control" name="attribute_value" value="">
                            </div>
                            <div class="col-4 d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-primary me-2 btn-sent-attribute">Gửi</button>
                                <button type="button" class="btn btn-outline-danger btn-unsent-attribute">Hủy</button>
                            </div>
                        </div>
                        <div class="btn-group btn-group-sm w-100 mt-3" role="group" aria-label="Small button group">
                            <button type="button" class="btn btn-outline-primary add-all-value-attribute">Thêm toàn bộ</button>
                            <button type="button" class="btn btn-outline-primary delete-all-value-attribute">Xóa hết</button>
                            <button type="button" class="btn btn-outline-primary create-value-attribute">Tạo giá trị</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $("#new-attribute-row-container").append(newRow);
    }

    function handleRemoveAttribute() {
        var attributeRow = $(this).closest(".attribute-row");
        var attributeId = attributeRow.data("id");
        attributeRow.remove();
        selected = selected.filter((id) => id != attributeId);
        $("#attributeSelect").val("").trigger("change");
    }

    function toggleAttributeContent() {
        var content = $(this)
            .closest(".attribute-row")
            .find(".attribute-content");
        content.toggle();
    }

    function showAttributeValueInput() {
        var inputContainer = $(this)
            .closest(".attribute-content")
            .find(".create-new-attribute_value");
        inputContainer.show();
    }

    function hideAttributeValueInput() {
        var inputContainer = $(this)
            .closest(".attribute-content")
            .find(".create-new-attribute_value");
        inputContainer.hide();
    }

    function handleAddAllValues() {
        var attributeRow = $(this).closest(".attribute-row");
        var attributeId = attributeRow.data("id");

        $.ajax({
            url: `http://127.0.0.1:8000/api/admin/attributes/get-by-id/${attributeId}`,
            method: "GET",
            success: function (response) {
                if (response.status === "success") {
                    var values = response.data.values;

                    // Xóa hết giá trị cũ trước khi thêm mới
                    var container = attributeRow.find(
                        "#attribute_value_current"
                    );
                    container.empty();

                    // Duyệt qua từng giá trị và thêm vào giao diện
                    $.each(values, function (key, value) {
                        container.append(`
                            <span class="badge bg-info m-1 p-2 attribute-value" data-key="${key}">
                                ${value} <span class="ms-2 text-bg-info remove-value" style="cursor: pointer;">&times;</span>
                            </span>
                        `);
                    });
                } else {
                    alert("Không thể lấy dữ liệu thuộc tính.");
                }
            },
            error: function () {
                alert("Lỗi khi lấy dữ liệu từ server.");
                console.error("URL:", this.url);
            },
        });
    }
    // Sự kiện xóa giá trị thuộc tính khi nhấn vào dấu &times;
    $(document).on("click", ".remove-value", function () {
        var badge = $(this).closest(".attribute-value");
        badge.remove(); // Xóa giá trị khỏi giao diện
    });

    function handleDeleteAllValues() {
        var attributeRow = $(this).closest(".attribute-row");
        attributeRow.find("input[placeholder='Giá trị thuộc tính']").val("");
    }

    function handleCreateNewAttributeValue() {
        var inputContainer = $(this).closest(".create-new-attribute_value");
        var inputValue = inputContainer
            .find("input[name='attribute_value']")
            .val();
        var attributeRow = $(this).closest(".attribute-row");
        var attributeId = attributeRow.data("id");

        $.ajax({
            url: `http://127.0.0.1:8000/api/admin/attributes/creat-values/${attributeId}`,
            method: "POST",
            data: { value: inputValue },
            success: function (response) {
                var data = response.data;
                var input = attributeRow.find(
                    "input[placeholder='Giá trị thuộc tính']"
                );
                input.val((input.val() ? input.val() + ", " : "") + data.value);
                inputContainer.hide();
            },
            error: function () {
                alert("Lỗi khi tạo giá trị mới.");
            },
        });
    }
    // Variant
    function handleGenerateVariants() {
        var variants = [];

        // Duyệt qua tất cả các dòng thuộc tính đã được thêm vào
        $(".attribute-row").each(function () {
            var attributeId = $(this).data("id");
            var attributeName = $(this).find(".attribute-title p").text();

            // Lấy tất cả các giá trị và id thuộc tính
            var values = [];
            $(this)
                .find(".attribute-value")
                .each(function () {
                    values.push({
                        id_value: $(this).data("key"),
                        value: $(this).text().trim().replace("×", "").trim(),
                    });
                });

            if (values.length > 0) {
                variants.push({
                    attribute_id: attributeId,
                    attribute_name: attributeName,
                    values: values,
                });
            }
        });

        // Tạo các tổ hợp biến thể
        var generatedVariants = generateCombinations(variants);

        // Hiển thị các biến thể trong layout
        renderVariantsLayout(generatedVariants);
    }

    function renderVariantsLayout(variants) {
        console.log(variant);
        var container = $("#container-variations");
        container.empty(); // Xóa nội dung cũ trong container

        if (variants.length === 0) {
            container.append("<p>Không có biến thể nào được tạo.</p>");
            return;
        }

        // Tạo layout cho từng biến thể
        variants.forEach((variant, index) => {
            var variantDetails = variant
                .map((v) => `${v.attribute_name}: ${v.value.value}`)
                .join(", ");

            var variantRow = `
                <div class="variant-row border rounded p-1 mb-1" data-id="${index}">
                    <div class="row mb-1 variant-title">
                        <div class="col-12 d-flex justify-content-between">
                            <h6 class="mb-0">#${
                                index + 1
                            } - ${variantDetails}</h6>
                            <div>
                                <button type="button" class="btn btn-outline-danger remove-variant me-3">Xóa</button>
                                <button type="button" class="btn btn-outline-primary edit-variant">Sửa</button>
                            </div>
                        </div>
                    </div>
    
                    <div class="row mb-1 variant-content" style="display:none;">
                        <input type="hidden" name="variant_attribute_values_${index}" value='${JSON.stringify(
                variant
            )}' />
                        <div class="col-md-2 text-center">
                            <div class="image-placeholder border d-flex align-items-center justify-content-center image-variant" style="height: 100px; position: relative;">
                                <input type="file" class="form-control image-input" name="image_variant_${index}" accept="image/*" style="opacity: 0; position: absolute; width: 100%; height: 100%;">
                                <img src="" alt="Preview" class="image-preview" style="max-height: 100%; max-width: 100%; display: none;">
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Mã sản phẩm</label>
                                    <input type="text" class="form-control" name="product_code_${index}" placeholder="Nhập mã sản phẩm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Giá ($)</label>
                                    <input type="number" class="form-control" name="product_price_${index}" placeholder="Nhập giá sản phẩm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.append(variantRow);
        });

        // Gắn sự kiện để xử lý hiển thị ảnh xem trước sau khi layout được thêm vào
        $(".image-input").on("change", handleImageVariantPreview);
        $(".remove-variant").on("click", handleRemoveVariant);
    }

    function handleImageVariantPreview(event) {
        const file = event.target.files[0];

        if (!file || !file.type.startsWith("image/")) {
            alert("Vui lòng chọn một file ảnh hợp lệ!");
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const imagePreview = $(event.target).siblings(".image-preview");
            imagePreview.attr("src", e.target.result);
            imagePreview.show(); // Hiển thị ảnh xem trước
        };

        reader.readAsDataURL(file);
    }

    function generateCombinations(variants) {
        if (variants.length === 0) return [];

        function combine(index, current) {
            if (index === variants.length) {
                return [current];
            }

            var result = [];
            var attribute = variants[index];

            attribute.values.forEach(function (value) {
                var newCombination = [
                    ...current,
                    {
                        attribute_id: attribute.attribute_id,
                        attribute_name: attribute.attribute_name,
                        value: value,
                    },
                ];
                result = result.concat(combine(index + 1, newCombination));
            });

            return result;
        }

        return combine(0, []);
    }
    function tonggleVariant() {
        var content = $(this).closest(".variant-row").find(".variant-content");
        content.toggle();
    }
    function handleRemoveVariant() {
        var variantRow = $(this).closest(".variant-row");

        variantRow.remove();
    }

    initEvents();
});
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $("#product-form").on("submit", function (e) {
        e.preventDefault(); // Ngăn chặn hành vi submit mặc định

        // 1. Thu thập và format dữ liệu
        let formData = prepareProductData();
        console.log("FormData trước khi gửi", formData);
        // 2. Gửi dữ liệu qua AJAX
        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data: formData, // Gửi formData đã chuẩn hóa
            contentType: false,
            processData: false,
            success: function (response) {
                alert("Thêm sản phẩm thành công!");
                console.log("Dữ liệu trả về từ server", response);
            },
            error: function (xhr) {
                alert("Có lỗi xảy ra khi thêm sản phẩm.");
                console.error(xhr.responseText);
            },
        });
    });
});

/**
 * Chuẩn bị và định dạng dữ liệu sản phẩm trước khi gửi
 */
function prepareProductData() {
    let formData = new FormData(document.querySelector("#product-form"));

    // Thêm dữ liệu text cơ bản
    let quillContent = quill ? quill.root.innerHTML : $("#description").val();
    formData.append("description", quillContent || "");
    formData.append("name", formData.get("name").trim() || "");
    formData.append("id_category", formData.get("id_category"));
    formData.append("id_brand", formData.get("id_brand"));

    // Thu thập dữ liệu từng biến thể sản phẩm
    $(".variant-row").each(function (index) {
        // Mã sản phẩm và giá
        let productCode = $(this)
            .find(`input[name='product_code_${index}']`)
            .val();
        let productPrice = $(this)
            .find(`input[name='product_price_${index}']`)
            .val();

        // Lấy giá trị thuộc tính từ input ẩn
        let attributeValues = $(this)
            .find(`input[name='variant_attribute_values_${index}']`)
            .val();
        attributeValues = JSON.parse(attributeValues); // Chuyển chuỗi JSON thành object

        // Thêm dữ liệu vào FormData
        formData.append(`variants[${index}][code]`, productCode);
        formData.append(`variants[${index}][price]`, productPrice);
        formData.append(
            `variants[${index}][attributes]`,
            JSON.stringify(attributeValues)
        );

        // Nếu có ảnh biến thể, thêm vào FormData
        let imageInput = $(this).find(`input[name='image_variant_${index}']`)[0]
            .files[0];
        if (imageInput) {
            formData.append(`variants[${index}][image]`, imageInput);
        }
    });

    console.log("FormData gửi đi:", formData);
    return formData;
}
