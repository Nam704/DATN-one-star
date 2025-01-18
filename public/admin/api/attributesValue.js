$(document).ready(function() {
    const token = $('meta[name="csrf-token"]').attr('content');

    $('.delete-attribute-value').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this attribute value?')) {
            $.ajax({
                url: `/admin/attribute-values/${id}`,
                type: 'DELETE',
                data: { "_token": token },
                success: function(response) {
                    if (response.success) {
                        alert('Attribute value deleted successfully');
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to delete attribute value');
                }
            });
        }
    });

    $('.toggle-status').click(function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/admin/attribute-values/${id}/toggle-status`,
            type: 'POST',
            data: { "_token": token },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });
});
