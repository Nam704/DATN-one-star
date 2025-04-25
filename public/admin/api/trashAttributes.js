$(document).ready(function() {
    // Thiết lập CSRF Token
    const token = $('meta[name="csrf-token"]').attr('content');

    // Chức năng khôi phục thuộc tính
    $('.restore-attribute').click(function() {
        const id = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn khôi phục thuộc tính này không?')) {
            $.ajax({
                url: `/admin/attributes/${id}/restore`,
                type: 'POST',
                data: {
                    "_token": token
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        if (response.action === 'restored') {
                            location.reload();
                        }
                    } else {
                        alert(response.message || 'Bạn không có quyền thực hiện hành động này.');
                    }
                },
                error: function() {
                    alert('Khôi phục thuộc tính thất bại');
                }
            });
        }
    });

    // Chức năng xóa vĩnh viễn thuộc tính
    $('.force-delete-attribute').click(function() {
        const id = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn xóa vĩnh viễn thuộc tính này không?')) {
            $.ajax({
                url: `/admin/attributes/${id}/force-delete`,
                type: 'DELETE',
                data: {
                    "_token": token
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message || 'Chỉ quản trị viên mới có quyền xóa vĩnh viễn.');
                    }
                },
                error: function() {
                    alert('Xóa vĩnh viễn thuộc tính thất bại');
                }
            });
        }
    });
});
