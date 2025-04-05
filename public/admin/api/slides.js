$(document).ready(function () {
    initializeCategoryHandlers();
    initializeAlbumImageHandler();
    initializeSlideImageHandler();
});

function initializeCategoryHandlers() {
    $("#show_add_category").on("click", showAddCategorySection);
    $("#cancel_add_category").on("click", hideAddCategorySection);
    $("#confirm_add_category").on("click", addNewCategory);
}

function initializeSlideImageHandler() {
    $("#slideImage").on("change", handleSlideImageSelection);
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
                alert("Thêm danh mục thành công !");
            } else {
                alert("Thêm danh mục thất bại!");
            }
        },
        error: function (xhr, status, error) {
            console.error("Lỗi:", error);
            alert("Đã xảy ra lỗi khi thêm danh mục!");
        }
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

/** --- Product Image Handler --- **/
function handleSlideImageSelection(event) {
    const file = event.target.files[0];

    if (!file || !file.type.startsWith("image/")) {
        alert("Vui lòng chọn một file ảnh hợp lệ!");
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        displaySlideImagePreview(e.target.result);
    };
    reader.readAsDataURL(file);
}

function displaySlideImagePreview(imageSrc) {
    $("#imagePreview").html(`
        <div class="position-relative d-inline-block" id="imageContainer" style="max-width: 200px;">
            <img src="${imageSrc}" alt="Ảnh slide" class="img-fluid rounded" style="width: 100%; height: auto;" />
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-image"
                    style="z-index: 10;">X</button>
        </div>
    `);

    $(".remove-image").on("click", function () {
        $("#imageContainer").remove();
        $("#slideImage").val("");
    });
}

/** --- Album Image Handlers --- **/

let selectedImages = [];

function openAlbumImageSelector() {
    $("#albumImages").click();
}

function handleAlbumImageSelection() {
    const files = $("#albumImages")[0].files;
    let newImages = [];

    for (let i = 0; i < files.length; i++) {
        if (selectedImages.length + newImages.length >= 5) {
            alert("Bạn chỉ được chọn tối đa 5 ảnh.");
            break;
        }

        if (!isImageAlreadySelected(files[i])) {
            newImages.push(files[i]);
        }
    }

    selectedImages = [...selectedImages, ...newImages];
    updateAlbumPreview();
}

function isImageAlreadySelected(file) {
    return selectedImages.some(img => img.name === file.name);
}

function updateAlbumPreview() {
    const albumPreview = $("#albumPreview");
    albumPreview.html("");

    selectedImages.forEach((image, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const colDiv = $(`
                <div class="col-3">
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-fluid rounded shadow" alt="Album Image">
                        <button type="button" class="btn btn-danger btn-sm remove-image-btn w-100" data-index="${index}">X</button>
                    </div>
                </div>
            `);

            colDiv.find(".remove-image-btn").on("click", function () {
                removeAlbumImage($(this).data("index"));
            });

            albumPreview.append(colDiv);
        };
        reader.readAsDataURL(image);
    });
}

function removeAlbumImage(index) {
    selectedImages.splice(index, 1);
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

//

$(document).ready(function () {
    $(".delete-slide-btn").click(function () {
        let slideId = $(this).data("id");

        Swal.fire({
            title: "Bạn có chắc chắn muốn xóa slide này?",
            text: "Hành động này không thể hoàn tác!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Xóa ngay!",
            cancelButtonText: "Hủy"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/slides/${slideId}`,
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire("Đã xóa!", response.message, "success");
                            $(`button[data-id="${slideId}"]`).closest("tr").remove();
                        } else {
                            Swal.fire("Lỗi!", response.message, "error");
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        Swal.fire("Lỗi!", "Không thể xóa slide.", "error");
                    }
                });
            }
        });
    });
});


