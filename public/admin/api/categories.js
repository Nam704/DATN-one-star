$(document).ready(function() {
    const token = $('meta[name="csrf-token"]').attr('content');

    // Initialize DataTable only if not already initialized
    if (!$.fn.DataTable.isDataTable('#fixed-header-datatable')) {
        $('#fixed-header-datatable').DataTable({
            fixedHeader: true,
            responsive: true
        });
    }

    $('.delete-category').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this category?')) {
            $.ajax({
                url: `/admin/categories/${id}`,
                type: 'DELETE',
                data: { "_token": token },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }
    });

    $('.toggle-status').click(function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/admin/categories/${id}/toggle-status`,
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
