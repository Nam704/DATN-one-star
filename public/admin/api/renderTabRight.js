$(document).ready(function () {
    initializeCategoryHandlers();
    initializeBrandHandlers();
    initializeProductImageHandler();
    initializeAlbumImageHandler();
    updateAlbumPreview();
});

function initializeCategoryHandlers() {
    $("#show_add_category").on("click", showAddCategorySection);
    $("#cancel_add_category").on("click", hideAddCategorySection);
    $("#confirm_add_category").on("click", addNewCategory);
}

function initializeBrandHandlers() {
    $("#show_add_brand").on("click", showAddBrandSection);
    $("#cancel_add_brand").on("click", hideAddBrandSection);
    $("#confirm_add_brand").on("click", addNewBrand);
}

function initializeProductImageHandler() {
    $("#productImage").on("change", handleProductImageSelection);
}

function initializeAlbumImageHandler() {
    $("#addAlbumImage").on("click", openAlbumImageSelector);
    $("#albumImages").on("change", handleAlbumImageSelection);
}

/** --- Category Handlers --- **/

function showAddCategorySection() {
    $("#add-category-section").show();
    $("#show_add_category").hide();
}

function hideAddCategorySection() {
    $("#add-category-section").hide();
    $("#show_add_category").show();
}

function addNewCategory() {
    let newCategoryName = $("#new_category_name").val();
    let parentCategoryId = $("#id_parent").val() || 0;

    if (!newCategoryName) {
        alert("Vui lòng nhập tên danh mục mới!");
        return;
    }

    $.ajax({
        type: "POST",
        url: "http://127.0.0.1:8000/api/admin/categories/add",
        data: { name: newCategoryName, id_parent: parentCategoryId },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                hideAddCategorySection();
                updateCategoryList(response.data);
            } else {
                alert("Thêm danh mục thất bại!");
            }
        },
        error: handleAjaxError,
    });
}

function updateCategoryList(newCategory) {
    let parentCategoryId = newCategory.id_parent;
    let newOption = $("<option>", {
        value: newCategory.id,
        text:
            parentCategoryId != 0 ? "-- " + newCategory.name : newCategory.name,
    }).prop("selected", true);

    if (parentCategoryId != 0) {
        $("#category_select option[value='" + parentCategoryId + "']").after(
            newOption
        );
    } else {
        $("#category_select").append(newOption);
        $("#id_parent").append(
            $("<option>", { value: newCategory.id, text: newCategory.name })
        );
    }

    $("#category_select").val(newCategory.id);
}

/** --- Brand Handlers --- **/

function showAddBrandSection() {
    $("#add-brand-section").show();
    $("#show_add_brand").hide();
}

function hideAddBrandSection() {
    $("#add-brand-section").hide();
    $("#show_add_brand").show();
}

function addNewBrand() {
    let newBrandName = $("#new_brand_name").val();

    if (!newBrandName) {
        alert("Vui lòng nhập tên thương hiệu mới!");
        return;
    }

    $.ajax({
        type: "POST",
        url: "http://127.0.0.1:8000/api/admin/brands/add",
        data: { name: newBrandName },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                alert("Thêm thương hiệu thành công!");
                hideAddBrandSection();
                updateBrandList(response.data);
            } else {
                alert("Thêm thương hiệu thất bại!");
            }
        },
    });
}

function updateBrandList(data) {
    $("#brand-select").append(
        $("<option>").val(data.id).text(data.name).prop("selected", true)
    );
}

/** --- Product Image Handler --- **/

function handleProductImageSelection(event) {
    const file = event.target.files[0];

    if (!file || !file.type.startsWith("image/")) {
        alert("Vui lòng chọn một file ảnh hợp lệ!");
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        displayProductImagePreview(e.target.result);
    };
    reader.readAsDataURL(file);
}

function displayProductImagePreview(imageSrc) {
    $("#imagePreview").html(`
        <div class="position-relative d-inline-block" id="imageContainer" style="max-width: 200px;">
            <img src="${imageSrc}" alt="Ảnh sản phẩm" class="img-fluid rounded" style="width: 100%; height: auto;" />
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-image"
                    style="z-index: 10;">X</button>
        </div>
    `);
}
// Xử lý sự kiện khi nhấn nút "X" để xóa ảnh
$(document).on("click", ".remove-image", function () {
    $("#imageContainer").remove();
    $("#productImage").val("");
});

/** --- Album Image Handlers --- **/
let selectedImages = [];

function openAlbumImageSelector() {
    $("#albumImages").click();
}

function handleAlbumImageSelection() {
    const files = $("#albumImages")[0].files;

    for (let i = 0; i < files.length; i++) {
        if (selectedImages.length + productAlbums.length >= 4) {
            alert("Bạn chỉ được chọn tối đa 4 ảnh.");
            break;
        }
        selectedImages.push(files[i]);
    }
    updateAlbumPreview();
}

function updateAlbumPreview() {
    const albumPreview = $("#albumPreview");
    albumPreview.html(""); // Xóa nội dung cũ

    let totalImages = 0;

    // Hiển thị album từ dữ liệu có sẵn (server)
    productAlbums.slice(0, 4).forEach((album, index) => {
        if (totalImages < 4) {
            const imageSrc = `http://127.0.0.1:8000${album.image_path}`;
            albumPreview.append(
                createAlbumImagePreviewElement(imageSrc, index, false)
            );
            totalImages++;
        }
    });

    // Hiển thị ảnh mới được chọn nếu chưa đủ 4 ảnh
    selectedImages.slice(0, 4 - totalImages).forEach((image, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            albumPreview.append(
                createAlbumImagePreviewElement(e.target.result, index, true)
            );
        };
        reader.readAsDataURL(image);
        totalImages++;
    });
}

function createAlbumImagePreviewElement(imageSrc, index, isNew) {
    const colDiv = $("<div>", { class: "col-3" });

    colDiv.html(`
        <div class="position-relative">
            <img src="${imageSrc}" class="img-fluid rounded shadow" alt="Album Image">
            <button type="button" class="btn btn-danger btn-sm remove-image-btn w-100" data-index="${index}" data-new="${isNew}">X</button>
        </div>
    `);

    colDiv.find(".remove-image-btn").on("click", function () {
        const isNewImage = $(this).data("new");
        removeAlbumImage(index, isNewImage);
    });

    return colDiv;
}

function removeAlbumImage(index, isNew) {
    if (isNew) {
        selectedImages.splice(index, 1);
    } else {
        productAlbums.splice(index, 1);
    }
    updateAlbumPreview();
}

/** --- Error Handling --- **/

function handleAjaxError(xhr) {
    if (xhr.status !== 200) {
        console.log(xhr.responseJSON.errors);
    } else {
        alert("Có lỗi xảy ra, vui lòng thử lại!");
    }
}
