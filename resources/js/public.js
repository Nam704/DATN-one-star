import "./bootstrap";

const userId = currentUserId; // Lấy ID người dùng hiện tại từ session hoặc truyền từ backend
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
        badge.textContent = data.count > 0 ? data.count : "0";
    }

    if (list) {
        // Xóa nội dung cũ
        list.innerHTML = "";
        var link = "";
        // Render danh sách thông báo mới
        data.notifications.forEach((notification) => {
            const item = document.createElement("a");
            if (notification.type == "imports") {
                link =
                    "http://127.0.0.1:8000/admin/imports/detail/" +
                    notification.goto_id;
            }
            if (notification.type == "products") {
                link =
                    "http://127.0.0.1:8000/admin/products/detail/" +
                    notification.goto_id;
            }
            item.href = link;
            item.className = "dropdown-item notify-item unread-noti";
            item.innerHTML = `
                <div class="notify-icon bg-primary-subtle">
                    <i class="mdi mdi-comment-account-outline text-primary"></i>
                </div>
                <p class="notify-details">${notification.title} from 
                ${notification.from_user_name}
                        <small class="noti-time">${new Date(
                            notification.created_at
                        ).toLocaleString()}</small>
                </p>
            `;
            item.addEventListener("click", (e) => {
                markNotificationAsRead(notification.id); // Gửi API
            });

            list.appendChild(item);
        });

        // Khởi tạo lại SimpleBar
        new SimpleBar(list, { autoHide: true });
    }
}

// Gọi API để lấy thông báo chưa đọc
function fetchUnreadNotifications() {
    fetch(`http://127.0.0.1:8000/api/admin/notifications/unread/${userId}`)
        .then((response) => response.json())
        .then((data) => {
            // console.log("Unread Notifications:", data);
            updateNotificationList(data, "notification");
        })
        .catch((error) => {
            console.error("Error fetching notifications:", error);
        });
}
// Hàm gửi API để chuyển trạng thái thông báo sang 'read' bằng Axios
function markNotificationAsRead(notificationId) {
    axios
        .post(
            `http://127.0.0.1:8000/api/admin/notifications/mark-read/${notificationId}`,
            {
                status: "read",
            }
        )
        .then((response) => {
            // window.location.href = link;
            console.log(`Notification ${notificationId} marked as read.`);
        })
        .catch((error) => {
            console.error("Error marking notification as read:", error);
        });
}

// Gọi hàm khi trang được tải
document.addEventListener("DOMContentLoaded", fetchUnreadNotifications);
