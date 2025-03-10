<?php


namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;
use App\Services\CartServiceSession as CartService;

class CartControllerSession extends Controller
{
    protected $cartService;

    /**
     * Inject CartService vào Controller
     */
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
     * Lấy giỏ hàng từ session
     */
    public function getCart()
    {
        return response()->json($this->cartService->getCart());
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(Request $request)
    {
        $response = $this->cartService->addToCart($request->input('id_variant'), $request->input('quantity'));
        $this->cartService->saveSessionCartToDatabase();
        return response()->json($response);
    }

    /**
     * Cập nhật số lượng sản phẩm
     */
    public function updateCart(Request $request)
    {
        $response = $this->cartService->updateCart($request->input('id_variant'), $request->input('quantity'));
        return response()->json($response);
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart(Request $request)
    {
        $response = $this->cartService->removeFromCart($request->input('id_variant'));
        return response()->json($response);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart()
    {
        $response = $this->cartService->clearCart();
        return response()->json($response);
    }

    /**
     * Chuyển giỏ hàng từ session sang database khi user đăng nhập
     */
    public function saveSessionCartToDatabase()
    {
        $response = $this->cartService->saveSessionCartToDatabase();
        return response()->json($response);
    }
}
