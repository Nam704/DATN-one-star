$(document).ready(function () {
    initializeCommentHandlers();
});

function initializeCommentHandlers() {
    $("#show_add_comment").on("click", showAddCommentSection);
    $("#cancel_add_comment").on("click", hideAddCommentSection);
    $("#confirm_add_comment").on("click", addNewComment);
}

/** --- Comment Handlers --- **/

function showAddCommentSection() {
    $("#add-comment-section").show();
    $("#show_add_comment").hide();
}

function hideAddCommentSection() {
    $("#add-comment-section").hide();
    $("#show_add_comment").show();
}

function addNewComment() {
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
