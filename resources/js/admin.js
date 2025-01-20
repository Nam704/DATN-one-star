import Echo from "laravel-echo";
import "./bootstrap";

const badge = document.getElementById("notification_badge");
const notificationList = document.getElementById("notification_simplebar");

// Hàm cập nhật giao diện thông báo
function updateNotification(event) {
    // Cập nhật badge
    if (badge) {
        const currentCount = parseInt(badge.textContent) || 0;
        badge.textContent = currentCount + 1;
    }

    // Cập nhật danh sách thông báo
    if (notificationList) {
        const newNotification = document.createElement("a");
        newNotification.href = "javascript:void(0);";
        newNotification.className = "dropdown-item notify-item";
        newNotification.innerHTML = `
            <div class="notify-icon bg-primary-subtle">
                <i class="mdi mdi-comment-account-outline text-primary"></i>
            </div>
            <p class="notify-details">${event.title}
                <small class="noti-time">${new Date(
                    event.created_at
                ).toLocaleString()}</small>
            </p>
        `;
        notificationList.prepend(newNotification); // Thêm thông báo mới vào đầu danh sách
    }
}

// Lắng nghe sự kiện từ kênh admin
window.Echo.private("admin").listen("AdminNotification", (event) => {
    console.log("New Admin Notification:", event);
    console.log("in chanel admin");
    // Hiển thị popup thông báo
    Toastify({
        text: `New notification: ${event.title}`,
        duration: 3000, // Hiển thị trong 3 giây
        gravity: "top", // Vị trí: trên cùng
        position: "right", // Vị trí: bên phải
        backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
        stopOnFocus: true, // Dừng khi hover vào popup
    }).showToast();

    // Cập nhật giao diện thông báo
    updateNotification(event);
});
