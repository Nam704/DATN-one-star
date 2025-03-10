<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function addToCart(Request $request)
    {
        $id = $request->input('id');
        $id_variant = $request->input('id_variant');
        $quantity = $request->input('quantity');
        $cart =  $this->cartService->addToCart($id, $id_variant, $quantity);
        return response()->json(
            [
                'success' => true,
                'message' => 'Thêm vào giỏ hàng thành công',
                'data' => $cart
            ]
        );
    }
}
