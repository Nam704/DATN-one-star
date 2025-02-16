<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['items.productVariant.product'])->first();
        return view('client.layouts.cart', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        $productVariantId = $request->id_product_variant;
        $quantity = $request->quantity ?? 1;

        $cart = Cart::firstOrCreate(['id_user' => 1]); // Temporary using user_id 1

        $productVariant = ProductVariant::findOrFail($productVariantId);

        $cartItem = CartItem::updateOrCreate(
            [
                'id_cart' => $cart->id,
                'id_product_variant' => $productVariantId,
            ],
            [
                'quantity' => \DB::raw('quantity + ' . $quantity),
                'price' => $productVariant->price
            ]
        );

        return redirect()->route('client.cart')->with('success', 'Product added to cart successfully');
    }

    public function removeFromCart($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return redirect()->route('client.cart')->with('success', 'Product removed from cart successfully');
    }

    public function updateQuantity(Request $request, $id)
{
    $cartItem = CartItem::findOrFail($id);
    $cartItem->quantity = $request->quantity;
    $cartItem->save();

    // Lấy giỏ hàng và tổng tiền sau khi cập nhật
    $cart = Cart::with('items')->first(); // Bạn có thể cần lấy giỏ hàng của người dùng hiện tại
    $cartTotal = $cart->getTotal();

    return response()->json([
        'success' => true,
        'total' => $cartItem->price * $cartItem->quantity,
        'cartTotal' => $cartTotal
    ]);
}

    
// CartController.php
public function applyVoucher(Request $request)
{
    $voucher = Voucher::where('code', $request->voucher_code)
        ->where('quantity', '>', 0)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->first();

    if (!$voucher) {
        return response()->json([
            'success' => false,
            'message' => 'Mã giảm giá không hợp lệ'
        ]);
    }

    $cart = Cart::first();
    $subtotal = $cart->getSubtotal();
    $discount = $voucher->discount_amount;
    $total = $subtotal - $discount;

    return response()->json([
        'success' => true,
        'subtotal' => number_format($subtotal) . 'đ',
        'discount' => number_format($discount) . 'đ',
        'total' => number_format($total) . 'đ'
    ]);
}
public function updateAll(Request $request)
{
    foreach($request->items as $item) {
        CartItem::where('id', $item['id'])->update([
            'quantity' => $item['quantity']
        ]);
    }

    $cart = Cart::with('items')->first();
    $subtotal = $cart->getSubtotal();
    $total = $cart->getTotal();

    return response()->json([
        'subtotal' => $subtotal,
        'total' => $total
    ]);
}
}
