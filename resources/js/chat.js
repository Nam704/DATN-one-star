import "./bootstrap";
console.log("Hello from chat.js");
// Lắng nghe sự kiện "MessageSent" từ kênh "chat"
window.Echo.channel("chat").listen("MessageSent", (e) => {
    console.log(e.message); // Tin nhắn nhận được
    appendMessage(e.message, e.user); // Gọi hàm appendMessage để hiển thị tin nhắn
});

// Hàm thêm tin nhắn vào giao diện
function appendMessage(message, user) {
    const messageBox = document.getElementById("message-box");
    const messageElement = document.createElement("li");
    messageElement.classList.add("clearfix");

    // Tạo cấu trúc tin nhắn mới
    messageElement.innerHTML = `
        <div class="chat-avatar">
            <img src="" alt="user">
            <i>10:00</i>
        </div>
        <div class="conversation-text">
            <div class="ctext-wrap">
                <i>${user}</i>
                <p>${message}</p>
            </div>
        </div>
    `;

    messageBox.appendChild(messageElement);

    // Cuộn xuống cuối cùng để thấy tin nhắn mới
    messageBox.scrollTop = messageBox.scrollHeight;
}
// Gửi tin nhắn khi nhấn nút
document
    .getElementById("send-message-btn")
    .addEventListener("click", function (event) {
        event.preventDefault();

        const messageInput = document.getElementById("message-input");
        const message = messageInput.value.trim();

        if (message === "") {
            alert("Vui lòng nhập tin nhắn");
            return;
        }

        // Gửi tin nhắn qua API
        axios
            .post("/chat/send-message", {
                message: message,
            })
            .then((response) => {
                console.log(response.data);
                messageInput.value = ""; // Xóa nội dung sau khi gửi
            })
            .catch((error) => {
                console.error("Error sending message:", error);
            });
    });

// Hàm thêm tin nhắn vào giao diện
