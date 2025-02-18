$(document).ready(function() {
    const token = $('meta[name="csrf-token"]').attr('content');

    if (!$.fn.DataTable.isDataTable('#fixed-header-datatable')) {
        $('#fixed-header-datatable').DataTable({
            fixedHeader: true,
            responsive: true
        });
    }

    $('.restore-category').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to restore this category?')) {
            $.ajax({
                url: `/admin/categories/${id}/restore`,
                type: 'POST',
                data: { "_token": token },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to restore category');
                }
            });
        }
    });

    $('.force-delete-category').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to permanently delete this category?')) {
            $.ajax({
                url: `/admin/categories/${id}/force-delete`,
                type: 'DELETE',
                data: { "_token": token },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to delete category permanently');
                }
            });
        }
    });
});
