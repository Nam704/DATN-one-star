document.addEventListener("DOMContentLoaded", function () {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // Chức năng xóa thương hiệu
    document.querySelectorAll('.delete-brand').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('Bạn có chắc chắn muốn xóa thương hiệu này không?')) {
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
                        alert(data.message); // Sử dụng message từ server
                        location.reload();
                    } else {
                        alert(data.message || 'Xóa thương hiệu thất bại');
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Xóa thương hiệu thất bại');
                });
            }
        });
    });

    // Chức năng thay đổi trạng thái
    document.querySelectorAll(".toggle-status").forEach((button) => {
        button.addEventListener("click", function () {
            const id = this.dataset.id;
            const row = this.closest("tr");

            fetch(`/admin/brands/${id}/toggle-status`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                },
                credentials: "same-origin",
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        const statusBadge = row.querySelector(".badge");
                        const icon = this.querySelector("i");

                        if (data.newStatus === "active") {
                            this.classList.replace("btn-danger", "btn-success");
                            statusBadge.classList.replace(
                                "bg-danger",
                                "bg-success"
                            );
                            statusBadge.textContent = "Hoạt động";
                            icon.classList.replace(
                                "ri-lock-line",
                                "ri-lock-unlock-line"
                            );
                        } else {
                            this.classList.replace("btn-success", "btn-danger");
                            statusBadge.classList.replace(
                                "bg-success",
                                "bg-danger"
                            );
                            statusBadge.textContent = "Không hoạt động";
                            icon.classList.replace(
                                "ri-lock-unlock-line",
                                "ri-lock-line"
                            );
                        }

                        alert(data.message || "Cập nhật trạng thái thành công");
                    } else {
                        alert(data.message || "Cập nhật trạng thái thất bại");
                    }
                })
                .catch((error) => {
                    console.error("Lỗi:", error);
                    alert("Cập nhật trạng thái thất bại");
                });
        });
    });
});
