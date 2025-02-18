import Echo from "laravel-echo";
import "./bootstrap";

window.Echo.private("private-notifications").listen(
    "PrivateNotification",
    (event) => {
        alert(`New notification: ${event.title}`);
    }
);
window.Echo.private("imports").listen("ImportNotificationSent", (event) => {
    alert(`New notification: ${event.title}`);
});
