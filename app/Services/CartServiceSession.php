<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Cart_details;
use App\Models\Product_variant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartServiceSession
{
    /**
     * Lấy giỏ hàng từ session
     */
    public function getCart()
    {
        $user = Auth::user();
        if (!$user) {
            return Session::get('cart', []);
        }
        $cart = Cart::where('id_user', $user->id)->first();
        if (!$cart) {
            return Session::get('cart', []);
        }
        foreach ($cart->cartItems as $item) {
            $variant = Product_variant::find($item->id_variant);
            $item->sku = $variant->sku;
            $item->price = $variant->price;
            $item->image = $variant->images->url;
            $item->name = $variant->product->name;
        }
        return $cart->cartItems;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart($id_variant, $quantity)
    {
        // return Session::forget('cart');
        $variant = Product_variant::find($id_variant);
        if (!$variant) {
            return ['error' => 'Product not found'];
        }

        $cart = session()->get('cart');


        if (isset($cart[$id_variant])) {

            $cart[$id_variant]['quantity'] += (int) $quantity; // Đảm bảo cộng số
        } else {
            $cart[$id_variant] = [
                'id_variant' => $id_variant,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'quantity' => (int) $quantity,
                'image' => $variant->images->url,
                'name' => $variant->product->name,
            ];
        }


        session()->put('cart', $cart);


        return ['message' => 'Added to cart successfully', 'cart' => session()->get('cart')];
    }



    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function updateCart($id_variant, $quantity)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id_variant])) {
            $cart[$id_variant]['quantity'] = $quantity;
            Session::put('cart', $cart);
            return ['message' => 'Cart updated', 'cart' => $cart];
        }

        return ['error' => 'Product not found in cart'];
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart($id_variant)
    {
        $user = Auth::user();
        if ($user) {
            $cart = Cart::where('id_user', $user->id)->first();
            if ($cart) {
                $cartItem = $cart->cartItems()->where('id_variant', $id_variant)->first();
                if ($cartItem) {
                    $cartItem->delete();
                    return ['message' => 'Product removed', 'cart' => $cart->cartItems];
                }
            }
        } else {
            $cart = Session::get('cart', []);

            if (isset($cart[$id_variant])) {
                unset($cart[$id_variant]);
                Session::put('cart', $cart);
                return ['message' => 'Product removed', 'cart' => $cart];
            }
        }


        return ['error' => 'Product not found in cart'];
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart()
    {
        Session::forget('cart');
        return ['message' => 'Cart cleared'];
    }

    /**
     * Lưu giỏ hàng từ session vào database khi user đăng nhập
     */
    public function saveSessionCartToDatabase()
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return ['message' => 'No items in cart'];
        }

        // Kiểm tra user đăng nhập
        $user = Auth::user();
        if (!$user) {
            return ['error' => 'User not authenticated'];
        }

        // Tạo giỏ hàng trong database nếu chưa có
        $cartDb = Cart::firstOrCreate(['id_user' => $user->id]);
        foreach ($cart as $item) {
            $cartDetail = Cart_details::where('id_cart', $cartDb->id)
                ->where('id_variant', $item['id_variant'])
                ->first();

            if ($cartDetail) {
                $cartDetail->quantity += $item['quantity']; // Cộng dồn số lượng
                $cartDetail->save();
            } else {
                Cart_details::create([
                    'id_cart' => $cartDb->id,
                    'id_variant' => $item['id_variant'],
                    'quantity' => $item['quantity']
                ]);
            }
        }


        // Xóa giỏ hàng trong session sau khi lưu vào database
        Session::forget('cart');

        return $cartDb;
    }
}
