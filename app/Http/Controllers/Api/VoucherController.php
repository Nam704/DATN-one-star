<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product_variant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    public function applyCoupon(Request $request)
    {
        $couponCode = $request->input('coupon');
        $variantIds = $request->input('variants');
        $subtotal = $request->input('subtotal');

        if (!$couponCode || !$variantIds || !is_array($variantIds) || !$subtotal) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ',
            ], 400);
        }

        $voucher = Voucher::where('code', $couponCode)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('quantity', '>', 0)
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã coupon không hợp lệ hoặc đã hết hạn',
            ], 404);
        }

        // Lấy danh sách applies_to và xử lý thủ công nếu cần
        $appliesTo = $voucher->applies_to ?? [];
        if (is_string($appliesTo)) {
            $appliesTo = json_decode($appliesTo, true);
        }
        Log::info('applies_to sau khi xử lý:', [$appliesTo]);

        if (!is_array($appliesTo)) {
            Log::error('applies_to không phải mảng:', [$appliesTo]);
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu applies_to không hợp lệ trong cơ sở dữ liệu',
            ], 500);
        }

        $invalidVariants = [];
        foreach ($variantIds as $variantId) {
            $variant = Product_variant::find($variantId);
            if (!$variant) {
                $invalidVariants[] = $variantId;
                continue;
            }

            $product = $variant->product;
            if (!$product) {
                $invalidVariants[] = $variantId;
                continue;
            }

            // Sửa quan hệ Category (belongsTo)
            $categoryIds = $product->Category ? [$product->Category->id] : [];

            $isApplicable = false;
            foreach ($appliesTo as $apply) {
                if (strpos($apply, 'product_') === 0) {
                    $productId = str_replace('product_', '', $apply);
                    if ($product->id == $productId) {
                        $isApplicable = true;
                        break;
                    }
                } elseif (strpos($apply, 'category_') === 0) {
                    $categoryId = str_replace('category_', '', $apply);
                    if (in_array($categoryId, $categoryIds)) {
                        $isApplicable = true;
                        break;
                    }
                }
            }

            if (!$isApplicable) {
                $invalidVariants[] = $variantId;
            }
        }

        if (!empty($invalidVariants)) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon không áp dụng cho các biến thể: ' . implode(', ', $invalidVariants),
            ], 400);
        }

        if ($subtotal < $voucher->min_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher->min_amount, 0, ',', '.') . ' ₫ để áp dụng coupon',
            ], 400);
        }

        $discount = 0;
        if ($voucher->type === 'percentage') {
            $discount = ($subtotal * $voucher->discount_amount) / 100;
            if ($voucher->max_discount_amount && $discount > $voucher->max_discount_amount) {
                $discount = $voucher->max_discount_amount;
            }
        } else {
            $discount = $voucher->discount_amount;
        }

        return response()->json([
            'success' => true,
            'discount' => $discount,
        ]);
    }
}
