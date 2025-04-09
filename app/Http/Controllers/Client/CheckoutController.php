<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $paymentService;
    protected $voucherService;
    public function __construct(
        PaymentService $paymentService,
        VoucherService $voucherService
    ) {
        $this->paymentService = $paymentService;
        $this->voucherService = $voucherService;
    }
    public function payment(Request $request)
    {
        // $this->paymentService->vnpay_payment();
    }
    public function index()
    {
        $data = session()->get('checkout_data', []);
        Log::info($data);
        $user = auth()->user();

        if (empty($data)) {
            return redirect()->route('client.carts.viewCart')
                ->with('error', 'Không có dữ liệu thanh toán!');
        }

        if (!$user) {
            return redirect()->route('auth.getFormLogin')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục thanh toán!');
        }

        $id_address = $user->address->id;
        if (!$id_address) {
            return redirect()->route('client.user.myAccount')
                ->with('error', 'Vui lòng thêm địa chỉ mặc định trước khi thanh toán!');
        }

        // Calculate Cart Subtotal
        $cartSubtotal = 0;
        $variantIds = [];
        foreach ($data['variants'] as $item) {
            $cartSubtotal += $item['price'] * $item['quantity'];
            $variantIds[] = $item['id_variant'];
        }

        // Apply Coupon
        $couponCode = $data['coupon'] ?? null;
        $couponResult = $this->voucherService->applyCoupon($couponCode, $variantIds, $cartSubtotal);
        $discount = $couponResult['discount'] ?? 0;

        // Calculate Order Total
        $orderTotal = $cartSubtotal - $discount;

        return view('client.checkout.index', compact('data', 'user', 'id_address', 'cartSubtotal', 'discount', 'orderTotal'));
    }
    public function create(Request $request)
    {
        // Lấy dữ liệu từ request
        $variants = $request->input('variants', []);
        $coupon = $request->input('coupon');

        // Kiểm tra dữ liệu
        if (empty($variants)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có sản phẩm nào được chọn để thanh toán!',
            ], 400);
        }

        // Lưu dữ liệu vào session để sử dụng ở trang checkout
        $request->session()->put('checkout_data', [
            'variants' => $variants,
            'coupon' => $coupon,
        ]);

        // Trả về URL để chuyển hướng
        return response()->json([
            'success' => true,
            'redirectUrl' => route('client.checkout.index'),
        ]);
    }
}
