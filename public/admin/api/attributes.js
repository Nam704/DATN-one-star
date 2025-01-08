$(document).ready(function() {
    // CSRF Token setup
    const token = $('meta[name="csrf-token"]').attr('content');

    // Delete attribute functionality
    $('.delete-attribute').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this attribute?')) {
            $.ajax({
                url: `/admin/attributes/${id}`,
                type: 'DELETE',
                data: {
                    "_token": token
                },
                success: function(response) {
                    if (response.success) {
                        alert('Attribute deleted successfully');
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to delete attribute');
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
                    const newStatus = response.newStatus;
                    
                    if (newStatus === 'active') {
                        button.html('<i class="ri-lock-unlock-line"></i>');
                        button.removeClass('btn-danger').addClass('btn-success');
                        statusBadge.removeClass('bg-danger').addClass('bg-success').text('Active');
                    } else {
                        button.html('<i class="ri-lock-line"></i>');
                        button.removeClass('btn-success').addClass('btn-danger');
                        statusBadge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
                    }
                    
                    alert('Status updated successfully');
                }
            },
            error: function() {
                alert('Failed to update status');
            }
        });
    });
});
