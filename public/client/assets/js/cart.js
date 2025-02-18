$(document).ready(function() {
    // Quantity update handlers
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
        
        $.ajax({
            url: `/client/cart/update/${itemId}`,  // Updated URL path
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                quantity: newValue
            },
            success: function(response) {
                input.val(newValue);
                row.find('.product_total').text(response.total.toLocaleString() + 'đ');
            }
        });
    }
    
    

    // Remove item handler
    $('.remove-item').click(function() {
        const button = $(this);
        const itemId = button.data('id');
        const row = button.closest('tr');
    
        if(confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            $.ajax({
                url: `/client/cart/remove/${itemId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    row.remove();
                }
            });
        }
    });
    

    // Update cart button handler
    // In cart.js
$('#update-cart').click(function() {
    console.log('Update cart button clicked');
    let cartItems = [];
    $('.quantity-input').each(function() {
        const row = $(this).closest('tr');
        const itemId = $(this).data('item-id');
        const quantity = parseInt($(this).val());
        const price = parseInt(row.find('.product_price').text().replace(/[^0-9]/g, ''));
        
        cartItems.push({
            id: itemId,
            quantity: quantity,
            price: price
        });
        console.log('Cart item:', {id: itemId, quantity: quantity, price: price});
    });

    $.ajax({
        url: '/cart/update-all', // Thay bằng URL chính xác
        type: 'POST',
        data: {
            quantity: newQuantity,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            // Cập nhật tổng tiền cho từng sản phẩm
            row.find('.product_total').text(response.total.toLocaleString() + 'đ');
    
            // Cập nhật tổng tiền giỏ hàng
            $('.cart_subtotal .price').text(response.cartTotal.toLocaleString() + 'đ');
            $('.final_total .price').text(response.cartTotal.toLocaleString() + 'đ');
        },
        error: function() {
            alert('Có lỗi xảy ra khi cập nhật số lượng!');
        }
    });    
});


    // Voucher application handler
    $('#apply-coupon').click(function(e) {
        e.preventDefault();
        let code = $('input[name="voucher_code"]').val();
        
        $.ajax({
            type: 'POST',
            url: '/voucher/check',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                voucher_code: code
            },
            success: function(response) {
                if(response.success) {
                    let subtotal = parseInt($('.cart_subtotal .price').text().replace(/[^0-9]/g, ''));
                    let discount = response.discount;
                    let total = subtotal - discount;
                    
                    $('.cart_subtotal .price').text(subtotal.toLocaleString() + 'đ');
                    $('.discount_amount').text(discount.toLocaleString() + 'đ');
                    $('.final_total .price').text(total.toLocaleString() + 'đ');
                    
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Có lỗi xảy ra, vui lòng thử lại');
            }
        });
    });
});
