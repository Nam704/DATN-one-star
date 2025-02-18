<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;

class CartItemController extends Controller
{
    // Thêm sản phẩm vào giỏ hàng
    public function addItem(Request $request)
    {
        $request->validate([
            'id_product_variant' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrCreate(['id_user' => auth()->id()]);

        $productVariant = ProductVariant::findOrFail($request->id_product_variant);

        // Kiểm tra xem sản phẩm đã có trong giỏ chưa
        $cartItem = CartItem::where('id_cart', $cart->id)
                            ->where('id_product_variant', $request->id_product_variant)
                            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'id_cart' => $cart->id,
                'id_product_variant' => $request->id_product_variant,
                'quantity' => $request->quantity,
                'price' => $productVariant->price
            ]);
        }

        return response()->json(['message' => 'Sản phẩm đã được thêm vào giỏ hàng']);
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeItem($id)
    {
        $cartItem = CartItem::find($id);
        if($cartItem) {
            $cartItem->delete();
        }
        return response()->json(['success' => true]);
    }



    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateQuantity(Request $request, $id)
{
    try {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        
        return response()->json([
            'success' => true,
            'total' => $cartItem->price * $cartItem->quantity,
            'cartTotal' => $cartItem->cart->getTotal()
        ]);
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}




}
