document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Delete brand functionality
    document.querySelectorAll('.delete-brand').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('Are you sure you want to delete this brand?')) {
                fetch(`/admin/brands/${id}`, {
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
                        alert('Brand deleted successfully');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete brand');
                });
            }
        });
    });

    // Toggle status functionality  
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const row = this.closest('tr');
            
            fetch(`/admin/brands/${id}/toggle-status`, {
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
                    const statusBadge = row.querySelector('.badge');
                    const icon = this.querySelector('i');
                    
                    if (data.newStatus === 'active') {
                        this.classList.replace('btn-danger', 'btn-success');
                        statusBadge.classList.replace('bg-danger', 'bg-success');
                        statusBadge.textContent = 'Active';
                        icon.classList.replace('ri-lock-line', 'ri-lock-unlock-line');
                    } else {
                        this.classList.replace('btn-success', 'btn-danger');
                        statusBadge.classList.replace('bg-success', 'bg-danger');
                        statusBadge.textContent = 'Inactive';
                        icon.classList.replace('ri-lock-unlock-line', 'ri-lock-line');
                    }
                    
                    alert('Status updated successfully');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update status');
            });
        });
    });
});
