

$(document).ready(function () {
    // Khởi tạo DataTable
    const table = $('#fixed-header-datatable').DataTable({
        responsive: true,
        fixedHeader: true,
        autoWidth: false
    });

    // Lấy CSRF token
    const token = $('meta[name="csrf-token"]').attr('content');

    // Xử lý xóa giá trị thuộc tính
    $('.delete-form').on('submit', function (e) {
        e.preventDefault(); // Ngăn submit form mặc định

        const form = $(this);
        const id = form.data('id');
        const url = form.attr('action');

        // Hiển thị hộp thoại xác nhận
        if (confirm('Bạn có chắc chắn muốn xóa giá trị thuộc tính này không?')) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: token,
                    _method: 'DELETE'
                },
                success: function (response) {
                    if (response.success) {
                        // Chuyển hướng về danh sách với thông báo
                        window.location.href = '/admin/attribute_values?message=' + encodeURIComponent(response.message);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: response.message || 'Không có quyền thực hiện hành động này'
                        });
                    }
                },
                error: function (xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Xóa giá trị thuộc tính thất bại';
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: message
                    });
                }
            });
        }
    });

    // Xử lý chuyển đổi trạng thái
    $('.toggle-status').on('click', function () {
        const button = $(this);
        const id = button.data('id');
        const row = $('#row-' + id);
        const statusCell = row.find('td:eq(3)'); // Cột trạng thái

        $.ajax({
            url: `/admin/attribute_values/${id}/toggle-status`,
            type: 'POST',
            data: {
                _token: token
            },
            success: function (response) {
                if (response.success) {
                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Cập nhật giao diện cho admin
                    if (response.action === 'updated') {
                        const newStatus = response.newStatus;
                        if (newStatus === 'active') {
                            button.html('<i class="ri-lock-unlock-line"></i>');
                            button.removeClass('btn-danger').addClass('btn-success');
                            statusCell.html('<p style="color: rgb(88, 160, 88)">Đang hoạt động</p>');
                        } else {
                            button.html('<i class="ri-lock-line"></i>');
                            button.removeClass('btn-success').addClass('btn-danger');
                            statusCell.html('<p style="color: rgb(86, 86, 86)">Ngừng hoạt động</p>');
                        }
                    } else if (response.action === 'requested') {
                        // Cập nhật trạng thái "Chờ phê duyệt" cho nhân viên
                        statusCell.html('<p style="color: orange;">Chờ phê duyệt trạng thái</p>');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: response.message || 'Không có quyền thực hiện hành động này'
                    });
                }
            },
            error: function (xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Cập nhật trạng thái thất bại';
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: message
                });
            }
        });
    });
});
