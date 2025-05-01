import "../app.js";

$(document).ready(function () {
    const baseUrl = GlobalUtils.baseUrl;
    const userId = currentUserId;
    const $notificationBadge = $("#notification_badge");
    const $notificationList = $("#notification_simplebar");
    const $notificationTabs = $("#notification-tabs");
    const $clearAllLink = $("#notification_list .text-decoration-underline");
    const categories = ["all", "payment", "order", "promotion", "system"];
    let currentCategory = "all";

    // Khởi tạo Echo để lắng nghe các kênh thông báo
    function initializeEcho() {
        const userRole = "admin"; // Giả định vai trò được truyền từ server (admin, user, employee)
        const channels = [
            {
                name: `private-notifications.${userId}`,
                event: "PrivateNotification",
            },
            { name: "public-notifications", event: "PublicNotification" },
            {
                name: `${userRole}-notifications`,
                event: `${
                    userRole.charAt(0).toUpperCase() + userRole.slice(1)
                }Notification`,
            },
        ];

        channels.forEach(({ name, event }) => {
            window.Echo.private(name).listen(event, (notification) => {
                console.log(`New ${event}:`, notification);
                showToast(notification);
                updateNotification(notification);
                loadNotifications(currentCategory); // Làm mới danh sách
            });
        });
    }

    // Hiển thị thông báo Toast
    function showToast(notification) {
        const priorityColors = {
            high: "linear-gradient(to right, #ff4444, #ff6666)",
            medium: "linear-gradient(to right, #00b09b, #96c93d)",
            low: "linear-gradient(to right, #6c757d, #adb5bd)",
        };

        Toastify({
            text: `${notification.title}\n${notification.message}`,
            duration: 5000,
            gravity: "top",
            position: "right",
            backgroundColor:
                priorityColors[notification.priority] || priorityColors.medium,
            stopOnFocus: true,
            close: true,
        }).showToast();
    }

    // Cập nhật danh sách thông báo
    function updateNotification(notification) {
        const listContent = $notificationList.find(".simplebar-content");
        if (!listContent.length) {
            console.error("SimpleBar content area not found!");
            return;
        }

        // Cập nhật badge
        const currentCount = parseInt($notificationBadge.text()) || 0;
        $notificationBadge.text(currentCount + 1);

        // Tạo phần tử thông báo
        const priorityClass =
            {
                high: "border-danger text-danger",
                medium: "border-info",
                low: "border-secondary",
            }[notification.priority] || "";

        const item = $("<a>", {
            href: notification.goto_route
                ? `${baseUrl}/${notification.goto_route.replace(".", "/")}${
                      notification.goto_id ? "/" + notification.goto_id : ""
                  }`
                : "#",
            class: `dropdown-item notify-item ${
                notification.status === "unread" ? "unread-noti" : ""
            } ${priorityClass}`,
            "data-id": notification.id,
        }).html(`
            <div class="notify-icon bg-${
                notification.category || "system"
            }-subtle">
                <i class="mdi mdi-${getIconForCategory(
                    notification.category
                )} text-${notification.category || "system"}"></i>
            </div>
            <p class="notify-details">${notification.title}
                <small class="noti-time">${new Date(
                    notification.created_at
                ).toLocaleString()}</small>
            </p>
        `);

        listContent.prepend(item);
        if ($notificationList[0].SimpleBar) {
            $notificationList[0].SimpleBar.recalculate();
        }
    }

    // Lấy biểu tượng cho danh mục
    function getIconForCategory(category) {
        return (
            {
                payment: "credit-card",
                order: "package",
                promotion: "gift",
                system: "alert-circle",
            }[category] || "comment-account-outline"
        );
    }

    // Tải danh sách thông báo từ API
    function loadNotifications(category = "all") {
        const params = { per_page: 15 };
        if (category !== "all") params.category = category;

        $notificationList.html(
            '<div class="text-center p-2"><span class="spinner-border spinner-border-sm"></span> Đang tải...</div>'
        );
        axios
            .get(`${baseUrl}/api/notifications/user/${userId}`, { params })
            .then((response) => {
                const notifications = response.data.data;
                const listContent =
                    $notificationList.find(".simplebar-content");
                listContent.empty();

                if (!notifications.length) {
                    listContent.append(
                        '<p class="text-center p-2">Không có thông báo nào.</p>'
                    );
                    return;
                }

                notifications.forEach((notification) => {
                    const priorityClass =
                        {
                            high: "border-danger text-danger",
                            medium: "border-info",
                            low: "border-secondary",
                        }[notification.priority] || "";

                    const item = $("<a>", {
                        href: notification.goto_route
                            ? `${baseUrl}/${notification.goto_route.replace(
                                  ".",
                                  "/"
                              )}${
                                  notification.goto_id
                                      ? "/" + notification.goto_id
                                      : ""
                              }`
                            : "#",
                        class: `dropdown-item notify-item ${
                            notification.status === "unread"
                                ? "unread-noti"
                                : ""
                        } ${priorityClass}`,
                        "data-id": notification.id,
                    }).html(`
                        <div class="notify-icon bg-${
                            notification.category || "system"
                        }-subtle">
                            <i class="mdi mdi-${getIconForCategory(
                                notification.category
                            )} text-${notification.category || "system"}"></i>
                        </div>
                        <p class="notify-details">${notification.title}
                            <small class="noti-time">${new Date(
                                notification.created_at
                            ).toLocaleString()}</small>
                        </p>
                    `);

                    listContent.append(item);
                });

                // Cập nhật badge dựa trên số thông báo chưa đọc
                const unreadCount = notifications.filter(
                    (n) => n.status === "unread"
                ).length;
                $notificationBadge.text(unreadCount || "");
                if ($notificationList[0].SimpleBar) {
                    $notificationList[0].SimpleBar.recalculate();
                }
            })
            .catch((error) => {
                Toastify({
                    text: "Lỗi khi tải thông báo.",
                    duration: 3000,
                    backgroundColor:
                        "linear-gradient(to right, #ff4444, #ff6666)",
                }).showToast();
                console.error("Error loading notifications:", error);
            });
    }

    // Xử lý click vào thông báo
    $notificationList.on("click", ".notify-item", function (e) {
        e.preventDefault();
        const $item = $(this);
        const notificationId = $item.data("id");
        const href = $item.attr("href");

        // Đánh dấu thông báo là đã đọc
        axios
            .post(`${baseUrl}/api/notifications/${notificationId}/read`, {
                user_id: userId,
            })
            .then(() => {
                $item.removeClass("unread-noti");
                const currentCount = parseInt($notificationBadge.text()) || 0;
                $notificationBadge.text(
                    currentCount > 0 ? currentCount - 1 : ""
                );
            })
            .catch((error) => {
                console.error("Error marking notification as read:", error);
            });

        // Chuyển hướng nếu có href
        if (href !== "#") {
            window.location.href = href;
        }
    });

    // Xử lý chuyển đổi tab danh mục
    $notificationTabs.on("click", ".nav-link", function (e) {
        e.preventDefault();
        $notificationTabs.find(".nav-link").removeClass("active");
        $(this).addClass("active");
        currentCategory = $(this).data("category");
        loadNotifications(currentCategory);
    });

    // Xử lý xóa tất cả thông báo
    $clearAllLink.on("click", function (e) {
        e.preventDefault();
        axios
            .post(`${baseUrl}/api/notifications/clear-all`, { user_id: userId })
            .then(() => {
                $notificationList
                    .find(".simplebar-content")
                    .empty()
                    .append(
                        '<p class="text-center p-2">Không có thông báo nào.</p>'
                    );
                $notificationBadge.text("");
                Toastify({
                    text: "Đã xóa tất cả thông báo.",
                    duration: 3000,
                    backgroundColor:
                        "linear-gradient(to right, #00b09b, #96c93d)",
                }).showToast();
            })
            .catch((error) => {
                Toastify({
                    text: "Lỗi khi xóa thông báo.",
                    duration: 3000,
                    backgroundColor:
                        "linear-gradient(to right, #ff4444, #ff6666)",
                }).showToast();
                console.error("Error clearing notifications:", error);
            });
    });

    // Khởi tạo tab danh mục
    const tabHtml = categories
        .map(
            (cat) => `
                <li class="nav-item">
                    <a class="nav-link ${
                        cat === "all" ? "active" : ""
                    }" href="#" data-category="${cat}">
                        ${
                            cat === "all"
                                ? "Tất cả"
                                : cat.charAt(0).toUpperCase() + cat.slice(1)
                        }
                    </a>
                </li>
            `
        )
        .join("");
    $notificationTabs.html(tabHtml);

    // Khởi tạo
    initializeEcho();
    loadNotifications();
});
