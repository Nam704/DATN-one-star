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
