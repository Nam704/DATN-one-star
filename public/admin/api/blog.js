$(document).ready(function () {
    initializeCategoryHandlers();
    initializeTagHandlers();
    initializeBlogImageHandler();
});

function initializeCategoryHandlers() {
    $("#show_add_category").on("click", showAddCategorySection);
    $("#cancel_add_category").on("click", hideAddCategorySection);
    $("#confirm_add_category").on("click", addNewCategory);
}

function initializeTagHandlers() {
    $("#show_add_tag").on("click", showAddTagSection);
    $("#cancel_add_tag").on("click", hideAddTagSection);
    $("#confirm_add_tag").on("click", addNewTag);
}

function initializeBlogImageHandler() {
    $("#blogImage").on("change", handleBlogImageSelection);
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
    let newCategoryName = $("#new_category_name").val().trim();

    if (!newCategoryName) {
        alert("Vui lòng nhập tên danh mục mới!");
        return;
    }

    $.ajax({
        type: "POST",
        url: "http://127.0.0.1:8000/api/admin/categoryBlog/add",
        data: { name: newCategoryName },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                hideAddCategorySection();
                updateCategoryList();
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


function updateCategoryList() {
    $.ajax({
        type: "GET",
        url: "http://127.0.0.1:8000/api/admin/categoryBlog/list", // API lấy danh mục mới
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                let categorySelect = $("#category_select");
                categorySelect.empty(); // Xóa danh sách cũ
                categorySelect.append('<option value="">Chọn danh mục</option>');

                response.data.forEach(category => {
                    categorySelect.append(
                        $("<option>").val(category.id).text(category.name)
                    );
                });

                categorySelect.val(response.newCategoryId); // Chọn danh mục mới thêm vào
            }
        },
        error: function (xhr, status, error) {
            console.error("Lỗi khi cập nhật danh sách danh mục:", error);
        }
    });
}



/** --- Tag Handlers --- **/

function showAddTagSection() {
    $("#add-tag-section").show();
    $("#show_add_tag").hide();
}

function hideAddTagSection() {
    $("#add-tag-section").hide();
    $("#show_add_tag").show();
}

function addNewTag() {
    let newTagName = $("#new_tag_name").val().trim();

    if (!newTagName) {
        alert("Vui lòng nhập tên thẻ tag mới!");
        return;
    }

    $.ajax({
        type: "POST",
        url: "http://127.0.0.1:8000/api/admin/tags/add",
        data: { name: newTagName },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                alert("Thêm thẻ tag thành công!");
                hideAddTagSection();
                updateTagList(response.data);
            } else {
                alert("Thêm thẻ tag thất bại!");
            }
        },
        error: function (xhr, status, error) {
            console.error("Lỗi:", error);
            alert("Đã xảy ra lỗi khi thêm thẻ tag!");
        }
    });
}

function updateTagList(data) {
    $.ajax({
        type: "GET",
        url: "http://127.0.0.1:8000/api/admin/tags/list", // API lấy danh sách tags mới
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                let tagSelect = $("#tag-select");
                tagSelect.empty(); // Xóa danh sách cũ
                tagSelect.append('<option value="">Chọn thẻ tag</option>');

                response.data.forEach(tag => {
                    tagSelect.append(
                        $("<option>").val(tag.id).text(tag.name)
                    );
                });

                // Chọn tag mới thêm vào
                tagSelect.val(data.id);
            }
        },
        error: function (xhr, status, error) {
            console.error("Lỗi khi cập nhật danh sách thẻ tag:", error);
        }
    });
}

function setStatus(value) {
    document.getElementById('status').value = value;
}

/** --- Error Handling --- **/

function handleAjaxError(xhr) {
    if (xhr.status !== 200) {
        console.log(xhr.responseJSON.errors);
    } else {
        alert("Có lỗi xảy ra, vui lòng thử lại!");
    }
}


function prepareBlogData() {
    let formData = new FormData(document.querySelector("#blog-form"));

    let quillContent = document.querySelector("#content").value;
    formData.append("content", quillContent.trim() || "Nội dung chưa cập nhật");

    formData.append("title", formData.get("title").trim() || "");
    formData.append("slug", formData.get("slug").trim() || "");
    formData.append("category_id", formData.get("category_id"));
    formData.append("status", formData.get("status"));

    let thumbnailInput = document.querySelector("input[name='thumbnail']");
    if (thumbnailInput.files.length > 0) {
        formData.append("thumbnail", thumbnailInput.files[0]);
    }

    // Thu thập danh sách tag (nếu có)
    let tags = [];
    $(".tag-checkbox:checked").each(function () {
        tags.push($(this).val());
    });
    formData.append("tags", JSON.stringify(tags));

    console.log("FormData gửi đi:", formData);
    return formData;
}


$(document).ready(function () {
    $(".delete-btn").click(function () {
        let blogId = $(this).data("id");

        Swal.fire({
            title: "Bạn có chắc chắn muốn xóa?",
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
                    url: `/admin/blogs/${blogId}`,
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire("Đã xóa!", response.message, "success");
                            $(`button[data-id="${blogId}"]`).closest("tr").remove();
                        } else {
                            Swal.fire("Lỗi!", response.message, "error");
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        Swal.fire("Lỗi!", "Không thể xóa bài viết.", "error");
                    }
                });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.restore-value').forEach(button => {
        button.addEventListener('click', function () {
            let blogId = this.getAttribute('data-id');

            Swal.fire({
                title: "Khôi phục bài viết?",
                text: "Bài viết sẽ được khôi phục lại danh sách chính!",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Khôi phục",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/blogs/${blogId}/restore`, { 
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            "Content-Type": "application/json"
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Thành công!", data.message, "success");
                                document.querySelector(`button[data-id="${blogId}"]`).closest("tr").remove();
                            } else {
                                Swal.fire("Lỗi!", data.message, "error");
                            }
                        })
                        .catch(error => {
                            console.error('Lỗi:', error);
                            Swal.fire("Lỗi!", "Có lỗi xảy ra khi khôi phục.", "error");
                        });
                }
            });
        });
    });
});










