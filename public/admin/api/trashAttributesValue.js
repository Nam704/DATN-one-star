$(document).ready(function() {
    const token = $('meta[name="csrf-token"]').attr('content');

    $('.restore-attribute-value').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to restore this attribute value?')) {
            $.ajax({
                url: `/admin/attribute-values/${id}/restore`,
                type: 'POST',
                data: { "_token": token },
                success: function(response) {
                    if (response.success) {
                        alert('Attribute value restored successfully');
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to restore attribute value');
                }
            });
        }
    });

    $('.force-delete-attribute-value').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to permanently delete this attribute value?')) {
            $.ajax({
                url: `/admin/attribute-values/${id}/force-delete`,
                type: 'DELETE',
                data: { "_token": token },
                success: function(response) {
                    if (response.success) {
                        alert('Attribute value permanently deleted');
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to delete attribute value permanently');
                }
            });
        }
    });
});
