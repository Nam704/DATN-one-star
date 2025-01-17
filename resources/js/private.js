import "./bootstrap";
console.log("Hello from Vite!");
Echo.private("imports").listen("ImportNotificationSent", (event) => {
    alert(`New Import Request from Employee: ${event.message}`);
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
