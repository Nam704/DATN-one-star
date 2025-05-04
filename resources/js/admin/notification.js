import "../app.js";

$(document).ready(function () {
    const baseUrl = GlobalUtils.baseUrl;
    const userId = currentUserId;
    const $notificationBadge = $("#notification_badge");
    const $notificationList = $("#notification_simplebar");
    const $notificationTabs = $("#notification-tabs");
    const $clearAllLink = $("#clear_all");

    const categories = ["all", "payment", "order", "promotion", "system"];
    let currentCategory = "all";
    let simpleBarInstance;
    try {
        simpleBarInstance = new SimpleBar($notificationList[0], {
            autoHide: true,
            scrollbarMinSize: 25,
        });
        console.log("SimpleBar initialized successfully:", simpleBarInstance);
    } catch (error) {
        console.error("Failed to initialize SimpleBar:", error);
    }
    // Hàm tính thời gian tương đối (ví dụ: "1 min ago")
    function timeAgo(date) {
        const now = new Date();
        const past = new Date(date);
        const diffInSeconds = Math.floor((now - past) / 1000);

        if (diffInSeconds < 60) return `${diffInSeconds} seconds ago`;
        const diffInMinutes = Math.floor(diffInSeconds / 60);
        if (diffInMinutes < 60) return `${diffInMinutes} min ago`;
        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) return `${diffInHours} hours ago`;
        const diffInDays = Math.floor(diffInHours / 24);
        return `${diffInDays} days ago`;
    }

    // Ánh xạ danh mục thông báo với màu sắc và biểu tượng
    function getCategoryStyles(category) {
        const styles = {
            payment: {
                bg: "bg-success-subtle",
                text: "text-success",
                icon: "credit-card",
            },
            order: {
                bg: "bg-warning-subtle",
                text: "text-warning",
                icon: "package",
            },
            promotion: {
                bg: "bg-pink-subtle",
                text: "text-pink",
                icon: "gift",
            },
            system: {
                bg: "bg-primary-subtle",
                text: "text-primary",
                icon: "alert-circle",
            },
        };
        return (
            styles[category] || {
                bg: "bg-primary-subtle",
                text: "text-primary",
                icon: "comment-account-outline",
            }
        );
    }

    // Khởi tạo Echo để lắng nghe các kênh thông báo
    function initializeEcho() {
        const userRole = "admin";
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
                showToast(notification);
                updateNotification(notification);
                loadNotifications(currentCategory);
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
    // Trong hàm updateNotification
    function updateNotification(notification) {
        let listContent = $notificationList.find(".simplebar-content");
        if (!listContent.length) {
            $notificationList.html('<div class="simplebar-content"></div>');
            listContent = $notificationList.find(".simplebar-content");
        }

        const currentCount = parseInt($notificationBadge.text()) || 0;
        $notificationBadge.text(currentCount + 1);

        const priorityClass =
            {
                high: "border-danger text-danger",
                medium: "border-info",
                low: "border-secondary",
            }[notification.priority] || "";

        const { bg, text, icon } = getCategoryStyles(notification.category);

        const item = $("<a>", {
            href: notification.goto_route
                ? `${baseUrl}/${notification.goto_route.replace(".", "/")}${
                      notification.goto_id ? "/" + notification.goto_id : ""
                  }`
                : "#",
            class: `dropdown-item notify-item ${
                notification.status === "unread" ? "unread-noti" : "read-noti"
            } ${priorityClass}`,
            "data-id": notification.id,
            "data-category": notification.category,
        }).html(`
        <div class="notify-icon ${bg}">
            <i class="mdi mdi-${icon} ${text}"></i>
        </div>
        <p class="notify-details">${notification.title}
            <small class="noti-time">${timeAgo(notification.created_at)}</small>
        </p>
    `);

        // Thêm thông báo mới vào đầu danh sách
        listContent.prepend(item); // Đã đúng, giữ nguyên
        simpleBarInstance.recalculate();
    }

    // Trong hàm loadNotifications
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
                // console.log(notifications);
                let listContent = $notificationList.find(".simplebar-content");
                if (!listContent.length) {
                    $notificationList.html(
                        '<div class="simplebar-content"></div>'
                    );
                    listContent = $notificationList.find(".simplebar-content");
                }
                listContent.empty();

                if (!notifications.length) {
                    listContent.append(
                        '<p class="text-center p-2">Không có thông báo nào.</p>'
                    );
                    simpleBarInstance.recalculate();
                    return;
                }

                // Sắp xếp thông báo theo created_at giảm dần
                notifications.sort(
                    (a, b) => new Date(b.created_at) - new Date(a.created_at)
                );

                notifications.forEach((notification) => {
                    const priorityClass =
                        {
                            high: "border-danger text-danger",
                            medium: "border-info",
                            low: "border-secondary",
                        }[notification.priority] || "";

                    const { bg, text, icon } = getCategoryStyles(
                        notification.category
                    );

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
                                : "read-noti"
                        } ${priorityClass}`,
                        "data-id": notification.id,
                        "data-category": notification.category,
                    }).html(`
                    <div class="notify-icon ${bg}">
                        <i class="mdi mdi-${icon} ${text}"></i>
                    </div>
                    <p class="notify-details">${notification.title}
                        <small class="noti-time">${timeAgo(
                            notification.created_at
                        )}</small>
                    </p>
                `);

                    listContent.append(item);
                });

                const unreadCount = notifications.filter(
                    (n) => n.status === "unread"
                ).length;
                $notificationBadge.text(unreadCount || "");

                simpleBarInstance.recalculate();
            })
            .catch((error) => {
                Toastify({
                    text: "Lỗi khi tải thông báo.",
                    duration: 3000,
                    backgroundColor:
                        "linear-gradient(to right, #ff4444, #ff6666)",
                }).showToast();
            });
    }

    // Xử lý click vào thông báo
    $notificationList.on("click", ".notify-item", function (e) {
        e.preventDefault();
        const $item = $(this);
        const notificationId = $item.data("id");
        const category = $item.data("category");
        const href = $item.attr("href");

        // Đánh dấu thông báo là đã đọc
        axios
            .post(
                `${baseUrl}/api/admin/notifications/mark-read/${notificationId}`,
                { user_id: userId }
            )
            .then(() => {
                $item.removeClass("unread-noti").addClass("read-noti");
                const currentCount = parseInt($notificationBadge.text()) || 0;
                $notificationBadge.text(
                    currentCount > 0 ? currentCount - 1 : ""
                );
            })
            .catch((error) => {
                console.error("Error marking notification as read:", error);
            });

        // Nếu là thông báo đơn hàng, điều hướng đến trang chi tiết đơn hàng
        if (category === "order" && href !== "#") {
            window.location.href = href; // Điều hướng đến client.order.detail/{order_id}
        } else if (href !== "#") {
            window.location.href = href; // Điều hướng đến các route khác
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
                let listContent = $notificationList.find(".simplebar-content");
                if (!listContent.length) {
                    $notificationList.html(
                        '<div class="simplebar-content"></div>'
                    );
                    listContent = $notificationList.find(".simplebar-content");
                }
                listContent
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
