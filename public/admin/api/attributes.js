$(document).ready(function() {
    // CSRF Token setup
    const token = $('meta[name="csrf-token"]').attr('content');

    // Delete attribute functionality
    $('.delete-attribute').click(function() {
        const id = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn xóa thuộc tính này không?')) {
            $.ajax({
                url: `/admin/attributes/${id}`,
                type: 'DELETE',
                data: {
                    "_token": token
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        if (response.action === 'deleted') {
                            location.reload();
                        }
                    } else {
                        alert(response.message || 'Không có quyền thực hiện hành động này');
                    }
                },
                error: function() {
                    alert('Xóa thuộc tính thất bại');
                }
            });
        }
    });

    // Toggle status functionality
    $('.toggle-status').click(function() {
        const id = $(this).data('id');
        const button = $(this);
        const statusBadge = button.closest('tr').find('.badge');

        $.ajax({
            url: `/admin/attributes/${id}/toggle-status`,
            type: 'POST',
            data: {
                "_token": token
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    if (response.action === 'updated') {
                        const newStatus = response.newStatus;
                        if (newStatus === 'active') {
                            button.html('<i class="ri-lock-unlock-line"></i>');
                            button.removeClass('btn-danger').addClass('btn-success');
                            statusBadge.removeClass('bg-danger').addClass('bg-success').text('Kích hoạt');
                        } else {
                            button.html('<i class="ri-lock-line"></i>');
                            button.removeClass('btn-success').addClass('btn-danger');
                            statusBadge.removeClass('bg-success').addClass('bg-danger').text('Không kích hoạt');
                        }
                    }
                } else {
                    alert(response.message || 'Không có quyền thực hiện hành động này');
                }
            },
            error: function() {
                alert('Cập nhật trạng thái thất bại');
            }
        });
    });
});
