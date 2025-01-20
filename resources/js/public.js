import "./bootstrap";
console.log("hello vite");

const userId = currentUserId; // Lấy ID người dùng hiện tại từ session hoặc truyền từ backend
console.log("User ID:", userId);

// Hàm hiển thị thông báo

function updateNotificationList(data, type) {
    const listId =
        type === "notification"
            ? "notification_simplebar"
            : "message_simplebar";
    const badgeId =
        type === "notification" ? "notification_badge" : "message_badge";

    const list = document.getElementById(listId);
    const badge = document.getElementById(badgeId);

    if (badge) {
        badge.textContent = data.count > 0 ? data.count : "";
    }

    if (list) {
        // Xóa nội dung cũ
        list.innerHTML = "";

        // Render danh sách thông báo mới
        data.notifications.forEach((notification) => {
            const item = document.createElement("a");
            item.href = "javascript:void(0);";
            item.className = "dropdown-item notify-item";
            item.innerHTML = `
                <div class="notify-icon bg-primary-subtle">
                    <i class="mdi mdi-comment-account-outline text-primary"></i>
                </div>
                <p class="notify-details">${notification.title}
                        <small class="noti-time">${new Date(
                            notification.created_at
                        ).toLocaleString()}</small>
                </p>
            `;
            list.appendChild(item);
        });

        // Khởi tạo lại SimpleBar
        new SimpleBar(list, { autoHide: true });
    }
}

// Gọi API để lấy thông báo chưa đọc
function fetchUnreadNotifications() {
    fetch(`/api/admin/notifications/unread/${userId}`)
        .then((response) => response.json())
        .then((data) => {
            console.log("Unread Notifications:", data);
            updateNotificationList(data, "notification");
        })
        .catch((error) => {
            console.error("Error fetching notifications:", error);
        });
}

// Gọi hàm khi trang được tải
document.addEventListener("DOMContentLoaded", fetchUnreadNotifications);
