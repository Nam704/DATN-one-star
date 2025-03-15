document.querySelectorAll(".ql-toolbar").forEach(toolbar => toolbar.remove());
// Khởi tạo Quill editor
var quill = new Quill("#snow-editor", {
    theme: "snow",
    modules: {
        toolbar: {
            container: [
                [{ font: [] }, { size: [] }],
                ["bold", "italic", "underline", "strike"],
                [{ color: [] }, { background: [] }],
                [{ script: "super" }, { script: "sub" }],
                [
                    { header: [1, 2, 3, 4, 5, 6, false] },
                    "blockquote",
                    "code-block",
                ],
                [
                    { list: "ordered" },
                    { list: "bullet" },
                    { indent: "-1" },
                    { indent: "+1" },
                ],
                ["direction", { align: [] }],
                ["link"],
                ["clean"],
                ["image", "video"],
            ],
        },
    },
});
// Lấy nội dung Quill

// Cập nhật nội dung từ Quill vào input hidden trước khi gửi form
quill.on("text-change", function () {
    document.querySelector("#content").value = quill.root.innerHTML;
    console.log("Nội dung cập nhật:", document.querySelector("#content").value);
});
