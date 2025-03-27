import Echo from "laravel-echo";
import "./bootstrap";

$(document).ready(function () {
    window.Echo.private("private-notifications").listen(
        "PrivateNotification",
        (event) => {
            console.log("New private Notification:", event);

            // Hiển thị popup thông báo
            Toastify({
                text: `New notification: ${event.title}`,
                duration: 3000, // Hiển thị trong 3 giây
                gravity: "top", // Vị trí: trên cùng
                position: "right", // Vị trí: bên phải
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                stopOnFocus: true, // Dừng khi hover vào popup
                close: true, // Tự động đóng sau khi hiển thị
            }).showToast();

            // Cập nhật giao diện thông báo
            updateNotification(event);
        }
    );
});
console.log("Hello from private.js");

function updateNotification(event) {
    var notification = event;
    const badge = document.getElementById("notification_badge");
    const list = document.getElementById("notification_simplebar");

    // Cập nhật số lượng badge
    if (badge) {
        const currentCount = parseInt(badge.textContent) || 0;
        badge.textContent = currentCount + 1;
    }

    // Lấy phần tử nội dung thực tế của SimpleBar
    const listContent = list.querySelector(".simplebar-content");
    if (!listContent) {
        console.error("SimpleBar content area not found!");
        return;
    }

    var link = "";
    const item = document.createElement("a");
    if (notification.type == "imports") {
        link =
            "http://127.0.0.1:8000/admin/imports/detail/" +
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
    // item.addEventListener("click", (e) => {
    //     markNotificationAsRead(notification.id, link); // Gửi API
    // });

    // Thêm vào đầu danh sách nội dung
    listContent.prepend(item);

    // Cập nhật SimpleBar nếu cần
    if (list.SimpleBar) {
        list.SimpleBar.recalculate();
    }
}
window.Echo.private("imports").listen("ImportNotificationSent", (event) => {
    alert(`New notification: ${event.title}`);
});
