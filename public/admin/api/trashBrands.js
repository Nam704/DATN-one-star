document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.restore-brand').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = btn.dataset.id;
            if (!confirm('Bạn có chắc chắn muốn khôi phục thương hiệu này?')) return;

            fetch(`/admin/brands/${id}/restore`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                credentials: 'same-origin',
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message || 'Đã xảy ra lỗi');
                if (data.success) location.reload();
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi khi gửi yêu cầu khôi phục.');
            });
        });
    });

    document.querySelectorAll('.force-delete-brand').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = btn.dataset.id;
            if (!confirm('Xóa vĩnh viễn?')) return;

            fetch(`/admin/brands/${id}/force-delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message || 'Đã xảy ra lỗi');
                if (data.success) location.reload();
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi khi gửi yêu cầu xóa vĩnh viễn.');
            });
        });
    });
});
