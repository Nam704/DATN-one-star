<?php

namespace App\Http\Controllers\Web;

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
    
    // Lấy giỏ hàng của người dùng
    $cart = Cart::firstOrCreate(['id_user' => Auth::id()]);

    // Lấy thông tin biến thể sản phẩm
    $productVariant = ProductVariant::findOrFail($productVariantId);

    // Lấy thông tin cấu hình giá
    $priceConfig = PriceConfiguration::where('product_variant_id', $productVariantId)->first();
    
    // Xử lý giá từ cấu hình
    if ($priceConfig && $priceConfig->use_price_from == 'import') {
        // Lấy giá từ bảng import_details
        $importDetail = ImportDetail::where('id_product_variant', $productVariantId)->orderBy('created_at', 'desc')->first();
        $price = $importDetail ? $importDetail->price_per_unit : $productVariant->price;
    } else {
        // Lấy giá từ bảng product_variants
        $price = $productVariant->price;
    }

    // Cập nhật hoặc thêm sản phẩm vào giỏ hàng
    $cartItem = CartItem::updateOrCreate(
        [
            'id_cart' => $cart->id,
            'id_product_variant' => $productVariantId,
        ],
        [
            'quantity' => \DB::raw('quantity + ' . $quantity), // Cộng dồn số lượng nếu có
            'price' => $price // Lưu giá đã tính
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
    $newQuantity = $request->quantity;

    // Lấy biến thể sản phẩm từ cartItem
    $productVariant = $cartItem->productVariant;

    // Lấy cấu hình giá của biến thể sản phẩm
    $priceConfig = PriceConfiguration::where('product_variant_id', $productVariant->id)->first();

    // Xử lý giá theo cấu hình
    if ($priceConfig && $priceConfig->use_price_from == 'import') {
        $importDetail = ImportDetail::where('id_product_variant', $productVariant->id)->first();
        $price = $importDetail ? $importDetail->price_per_unit : $productVariant->price;
    } else {
        $price = $productVariant->price;
    }

    // Cập nhật số lượng và giá của sản phẩm
    $cartItem->quantity = $newQuantity;
    $cartItem->price = $price;
    $cartItem->save();

    // Cập nhật tổng tiền giỏ hàng
    $cart = Cart::with('items')->first();
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
    try {
        foreach($request->items as $item) {
            CartItem::where('id', $item['id'])->update([
                'quantity' => $item['quantity']
            ]);
        }

        $cart = Cart::with('items')->first();
        $total = $cart->getTotal();

        return response()->json([
            'success' => true,
            'total' => $total
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


public function getTotals()
{
    $cart = Cart::with('items')->first();
    
    return response()->json([
        'subtotal' => $cart ? $cart->getSubtotal() : 0,
        'total' => $cart ? $cart->getTotal() : 0
    ]);
}


public function removeItem($id)
{
    $cartItem = CartItem::find($id);
    if ($cartItem) {
        // Xóa sản phẩm khỏi giỏ hàng
        $cartItem->delete();

        // Lấy lại tổng tiền giỏ hàng sau khi xóa sản phẩm
        $cart = Cart::with('items')->first();
        $subtotal = $cart ? $cart->getSubtotal() : 0;
        $total = $cart ? $cart->getTotal() : 0;

        return response()->json([
            'success' => true,
            'subtotal' => number_format($subtotal) . 'đ',
            'total' => number_format($total) . 'đ'
        ]);
    }

    return response()->json(['success' => false]);
}

}
