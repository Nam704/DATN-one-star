$(document).ready(function() {
    // CSRF Token setup
    const token = $('meta[name="csrf-token"]').attr('content');

    // Restore functionality
    $('.restore-attribute').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to restore this attribute?')) {
            $.ajax({
                url: `/admin/attributes/${id}/restore`,
                type: 'POST',
                data: {
                    "_token": token
                },
                success: function(response) {
                    if (response.success) {
                        alert('Attribute restored successfully');
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to restore attribute');
                }
            });
        }
    });

    // Force Delete functionality
    $('.force-delete-attribute').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to permanently delete this attribute?')) {
            $.ajax({
                url: `/admin/attributes/${id}/force-delete`,
                type: 'DELETE',
                data: {
                    "_token": token
                },
                success: function(response) {
                    if (response.success) {
                        alert('Attribute permanently deleted');
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to delete attribute permanently');
                }
            });
        }
    });
});
