$(document).ready(function () {
    initializeAddressHandlers();
    initializeRoleHandlers();
    initializeUserImageHandler();
});

function initializeAddressHandlers() {
    $("#show_add_role").on("click", showAddRoleSection);
    $("#cancel_add_role").on("click", hideAddRoleSection);
    $("#confirm_add_role").on("click", addNewRole);
}

function initializeRoleHandlers() {
    $("#show_add_tag").on("click", showAddTagSection);
    $("#cancel_add_tag").on("click", hideAddTagSection);
    $("#confirm_add_tag").on("click", addNewTag);
}

function initializeUserImageHandler() {
    $("#blogImage").on("change", handleBlogImageSelection);
}

/** --- Role Handlers --- **/

function showAddRoleSection() {
    $("#add-role-section").show();
    $("#show_add_role").hide();
}

function hideAddRoleSection() {
    $("#add-role-section").hide();
    $("#show_add_role").show();
}

function addNewRole() {
    let newRoleName = $("#new_role_ name").val().trim();

    if (!newRoleName) {
        alert("Vui lòng nhập tên quyền mới!");
        return;
    }

    $.ajax({
        type: "POST",
        url: "http://127.0.0.1:8000/api/admin/categoryBlog/add",
        data: { name: newRoleName },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                hideAddRoleSection();
                updateRoleList();
                alert("Thêm quyền thành công !");
            } else {
                alert("Thêm quyền thất bại!");
            }
        },
        error: function (xhr, status, error) {
            console.error("Lỗi:", error);
            alert("Đã xảy ra lỗi khi thêm quyền!");
        }
    });
}


function updateRoleList() {
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
