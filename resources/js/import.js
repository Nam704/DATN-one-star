import "./bootstrap";
console.log("Hello from Vite!");
Pusher.logToConsole = true;
Echo.private("imports").listen("ImportNotificationSent", (event) => {
    console.log("Received event:", event);
    console.log("New Import Notification:", event.importData);
    // Hiển thị thông báo trên giao diện
    alert(`New Import Request from Employee: ${event.message}`);
    // if (event.importData) {
    //     console.log("Received Import Data:", event.importData);
    // } else {
    //     console.error("Import Data is null!");
    // }
});
function confirmImport(importId) {
    axios
        .post("/admin/confirm-import", {
            import_id: importId,
        })
        .then((response) => {
            alert(response.data.message);
        })
        .catch((error) => {
            console.error(error.response.data.message);
            alert("Failed to confirm import.");
        });
}
