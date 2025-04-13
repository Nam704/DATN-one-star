<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product_variant;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

    public function index()
    {
        $data = session()->get('checkout_data', []);
        // Log::info($data);
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
    public function create_old(Request $request)
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
    public function create(Request $request)
    {
        try {
            // Validate dữ liệu đầu vào
            $validator = Validator::make($request->all(), [
                'variants' => 'required|array|min:1',
                'variants.*.id_variant' => 'required|integer|exists:product_variants,id',
                'variants.*.price' => 'required|numeric|min:0',
                'variants.*.quantity' => 'required|integer|min:1',
                'coupon' => 'nullable|exists:vouchers,code',

            ], [
                // Thông báo lỗi tùy chỉnh
                'variants.required' => 'Không có sản phẩm nào được chọn để thanh toán.',
                'variants.min' => 'Phải chọn ít nhất một sản phẩm để thanh toán.',
                'variants.*.id_variant.required' => 'ID biến thể sản phẩm là bắt buộc.',
                'variants.*.id_variant.exists' => 'Biến thể sản phẩm không tồn tại.',
                'variants.*.price.required' => 'Giá sản phẩm là bắt buộc.',
                'variants.*.price.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0.',
                'variants.*.quantity.required' => 'Số lượng sản phẩm là bắt buộc.',
                'variants.*.quantity.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
                'coupon.exists' => 'Mã giảm giá không tồn tại.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Lấy dữ liệu từ request
            $variants = $request->input('variants', []);
            $coupon = $request->input('coupon');

            // Kiểm tra số lượng tồn kho của các biến thể
            foreach ($variants as $index => $inputVariant) {
                $variant = Product_variant::where('id', $inputVariant['id_variant'])->first();

                // Đã kiểm tra exists trong validation, nên $variant chắc chắn tồn tại
                if ($variant->quantity < $inputVariant['quantity']) {
                    throw new \Exception("Số lượng sản phẩm không đủ cho biến thể  {$inputVariant['sku']}. Hiện có: {$variant->quantity}, Yêu cầu: {$inputVariant['quantity']}");
                }
            }

            // Lưu dữ liệu vào session để sử dụng ở trang checkout
            $request->session()->put('checkout_data', [
                'variants' => $variants,
                'coupon' => $coupon,
            ]);

            // Trả về URL để chuyển hướng
            return response()->json([
                'success' => true,
                'message' => 'Dữ liệu thanh toán đã được lưu thành công.',
                'redirectUrl' => route('client.checkout.index'),
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation error in checkout create: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
                'data' => null,
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in checkout create: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 422); // Dùng 422 cho lỗi số lượng để nhất quán với validation
        }
    }
}
