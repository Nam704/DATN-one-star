$('.delete-comment').on('click', function (e) {
    e.preventDefault();

    if (!confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
        return;
    }

    const commentId = $(this).data('id'); // Lấy ID từ thuộc tính data-id
    const deleteUrl = '/admin/comments-product/' + commentId; 

    $.ajax({
        url: deleteUrl,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        success: function (response) {
            if (response.success) {
                alert('Bình luận đã được xóa');
                $('#comment-' + response.commentId).remove();
                window.location.href = '/admin/comments-product/';;
            } else {
                alert('Đã xảy ra lỗi: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            alert('Đã xảy ra lỗi: ' + error);
            console.log(xhr.responseText);
        }
    });
});


