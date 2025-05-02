<?php

namespace App\Http\Controllers\Client;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected  $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    function viewCart()
    {

        $cart = $this->cartService->getCart();


        // return $cart;
        return view('client.cart.index', compact('cart'));
    }
    /**
     * Lấy thông tin giỏ hàng hiện tại
     */
    public function getCart(): JsonResponse
    {
        try {
            $cart = $this->cartService->getCart();
            return response()->json([
                'success' => true,
                'data' => $cart
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(Request $request): JsonResponse
    {
        try {
            // Log::info($request->all());
            $validated = $request->validate([
                'id_variant' => 'required|exists:product_variants,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $result = $this->cartService->addToCart(
                $validated['id_variant'],
                $validated['quantity']
            );

            return response()->json([
                'success' => true,
                'message' => $result['success'] ?? $result['error'],
                'cart' => $this->cartService->getCart()
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm
     */
    public function updateCart(Request $request): JsonResponse
    {

        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
                'variantId' => 'required|exists:product_variants,id'
            ]);

            $result = $this->cartService->updateCart(
                $validated['variantId'],
                $validated['quantity']
            );

            return response()->json([
                'success' => isset($result['success']),
                'message' => $result['success'] ?? $result['error'],
                'cart' => $this->cartService->getCart()
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart(Request $request): JsonResponse
    {
        try {
            $variantId = $request->input('variantId');
            $result = $this->cartService->removeFromCart($variantId);

            return response()->json([
                'success' => isset($result['success']),
                'message' => $result['success'] ?? $result['error'],
                'cart' => $this->cartService->getCart()
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart(): JsonResponse
    {
        try {
            $result = $this->cartService->clearCart();

            return response()->json([
                'success' => isset($result['success']),
                'message' => $result['success'] ?? $result['error'],
                'cart' => []
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Xử lý ngoại lệ chung
     */
    private function handleException(\Exception $e): JsonResponse
    {
        report($e); // Log lỗi

        return response()->json([
            'success' => false,
            'message' => 'Đã xảy ra lỗi hệ thống',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
