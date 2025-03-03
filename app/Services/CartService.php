<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Cart_details;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CartService
{
    protected $cart;
    protected $cartItem;

    public function __construct(Cart $cart, Cart_details $cartItem)
    {
        $this->cart = $cart;
        $this->cartItem = $cartItem;
    }

    /**
     * Tạo giỏ hàng cho người dùng nếu chưa có
     */
    public function store($id)
    {
        return $this->cart->firstOrCreate(['id_user' => $id]);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart($id, $id_variant, $quantity)
    {
        try {
            // Kiểm tra và tạo giỏ hàng nếu chưa có
            $cart = $this->store($id);

            // Bắt đầu transaction để đảm bảo dữ liệu không bị lỗi
            return DB::transaction(function () use ($cart, $id_variant, $quantity) {
                $cartItem = $this->cartItem
                    ->where('id_cart', $cart->id)
                    ->where('id_variant', $id_variant)
                    ->first();

                if ($cartItem) {
                    // Cập nhật số lượng sản phẩm nếu đã tồn tại
                    $cartItem->increment('quantity', $quantity);
                } else {
                    // Thêm sản phẩm mới vào giỏ hàng
                    $this->cartItem->create([
                        'id_cart' => $cart->id,
                        'id_variant' => $id_variant,
                        'quantity' => $quantity
                    ]);
                }

                // Trả về giỏ hàng với các mục đã cập nhật
                return $cart->load('cartItems');
            });
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
