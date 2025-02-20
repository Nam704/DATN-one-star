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
            url: `/client/cart/update/${itemId}`,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { quantity: newValue },
            success: function(response) {
                input.val(newValue);
                row.find('.product_total').text(response.total.toLocaleString() + 'đ');
                updateCartTotals();
                updateMiniCartTotals();
            }
        });
    }

    // Remove item handler with mini cart sync
    $('.remove-item').click(function() {
        const button = $(this);
        const itemId = button.data('id');
        const cartRow = button.closest('tr') || button.closest('.cart_item');
        const miniCartItem = $(`.mini_cart .cart_item[data-id="${itemId}"]`);
        const mainCartRow = $(`.cart_page tr:has(button[data-id="${itemId}"])`);
    
        if(confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            $.ajax({
                url: `/client/cart/remove/${itemId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        // Remove from both locations
                        mainCartRow.fadeOut(400, function() { $(this).remove(); });
                        miniCartItem.fadeOut(400, function() { $(this).remove(); });
                        
                        // Update cart count and totals
                        $('.cart_quantity').text(response.itemCount);
                        updateCartTotals();
                        
                        // Check empty states
                        if($('.cart_page tbody tr').length === 0) {
                            $('.cart_page tbody').append('<tr><td colspan="6" class="text-center">Your cart is empty</td></tr>');
                        }
                        if($('.mini_cart .cart_item').length === 0) {
                            $('.mini_cart').append('<p class="text-center">Giỏ hàng trống</p>');
                        }
                    }
                }
            });
        }
    });
    
    

    // Update cart totals
    function updateCartTotals() {
        $.ajax({
            url: '/client/cart/get-totals',
            method: 'GET',
            success: function(response) {
                $('.cart_subtotal .price').text(response.total.toLocaleString() + 'đ');
                $('.final_total .price').text(response.total.toLocaleString() + 'đ');
            }
        });
    }

    // Update mini cart totals
    function updateMiniCartTotals() {
        $.ajax({
            url: '/client/cart/get-totals',
            method: 'GET',
            success: function(response) {
                $('.mini_cart_table .cart_total .price').text(response.total.toLocaleString() + 'đ');
            }
        });
    }

    // Voucher handler
    $('#apply-coupon').click(function(e) {
        e.preventDefault();
        let code = $('input[name="voucher_code"]').val();
        
        $.ajax({
            type: 'POST',
            url: '/client/apply-voucher',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                voucher_code: code
            },
            success: function(response) {
                if(response.success) {
                    $('.cart_subtotal .price').text(response.subtotal.toLocaleString() + 'đ');
                    $('.discount_amount').text(response.discount.toLocaleString() + 'đ');
                    $('.final_total .price').text(response.total.toLocaleString() + 'đ');
                    updateMiniCartTotals();
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
