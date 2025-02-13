$(document).ready(function() {
    $('.btn-increase').click(function() {
        let input = $(this).siblings('.quantity-input');
        let newValue = parseInt(input.val()) + 1;
        if (newValue <= 100) {
            updateQuantity(input, newValue);
        }
    });

    $('.btn-decrease').click(function() {
        let input = $(this).siblings('.quantity-input');
        let newValue = parseInt(input.val()) - 1;
        if (newValue >= 1) {
            updateQuantity(input, newValue);
        }
    });

    function updateQuantity(input, newValue) {
        const itemId = input.data('item-id');
        const row = input.closest('tr');
        
        console.log('Updating quantity:', {itemId, newValue}); // Debug log

        $.ajax({
            url: `/cart/update/${itemId}`,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                quantity: newValue
            },
            success: function(response) {
                console.log('Response:', response); // Debug log
                input.val(newValue);
                row.find('.product_total').text('$' + response.total.toFixed(2));
                $('.cart_amount').text('$' + response.cartTotal.toFixed(2));
            },
            error: function(xhr, status, error) {
                console.log('Error:', error); // Debug log
            }
        });
    }
});

// Thêm xử lý xóa sản phẩm
$('.remove-item').click(function() {
    const button = $(this);
    const itemId = button.data('id');
    const row = button.closest('tr');

    if(confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        $.ajax({
            url: `/cart/remove/${itemId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Success:', response);
                row.fadeOut(400, function() {
                    row.remove();
                });
                $('.cart_amount').text('$' + response.cartTotal.toFixed(2));
                alert('Đã xóa sản phẩm khỏi giỏ hàng');
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                alert('Có lỗi xảy ra khi xóa sản phẩm');
            }
        });
    }
});
