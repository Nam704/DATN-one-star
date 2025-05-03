import axios from "axios";
window.axios = axios;

// Cấu hình header mặc định cho Axios
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Thêm cấu hình CSRF token từ meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken.content;
} else {
    console.warn("CSRF token not found in meta tag.");
}

// Thêm thư viện Toastify
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
window.Toastify = Toastify;

// Cấu hình Laravel Echo và Pusher
import Echo from "laravel-echo";
import Pusher from "pusher-js";
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
    wsHost: import.meta.env.VITE_PUSHER_HOST
        ? import.meta.env.VITE_PUSHER_HOST
        : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
    enabledTransports: ["ws", "wss"],
});
