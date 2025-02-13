document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Restore brand functionality
    document.querySelectorAll('.restore-brand').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('Are you sure you want to restore this brand?')) {
                fetch(`/admin/brands/${id}/restore`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Brand restored successfully');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to restore brand');
                });
            }
        });
    });

    // Force delete functionality
    document.querySelectorAll('.force-delete-brand').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('Are you sure you want to permanently delete this brand? This action cannot be undone.')) {
                fetch(`/admin/brands/${id}/force-delete`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Brand permanently deleted');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete brand permanently');
                });
            }
        });
    });
});
