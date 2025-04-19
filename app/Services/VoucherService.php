<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\Product_variant;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VoucherService
{
    /**
     * Áp dụng coupon vào giỏ hàng
     */
    public function applyCoupon($couponCode, $variantIds, $subtotal)
    {
        if (!$couponCode || !$variantIds || !is_array($variantIds) || !$subtotal) {
            return [
                'success' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ',
                'discount' => 0,
            ];
        }

        $voucher = Voucher::where('code', $couponCode)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('quantity', '>', 0)
            ->first();

        if (!$voucher) {
            return [
                'success' => false,
                'message' => 'Mã coupon không hợp lệ hoặc đã hết hạn',
                'discount' => 0,
            ];
        }

        $appliesTo = $voucher->applies_to ?? [];
        if (!is_array($appliesTo)) {
            Log::error('applies_to không phải mảng:', [$appliesTo]);
            return [
                'success' => false,
                'message' => 'Dữ liệu applies_to không hợp lệ',
                'discount' => 0,
            ];
        }

        $invalidVariants = [];
        foreach ($variantIds as $variantId) {
            $variant = Product_variant::find($variantId);
            if (!$variant) {
                $invalidVariants[] = "ID_$variantId (không tìm thấy)";
                continue;
            }

            $product = $variant->product;
            if (!$product) {
                $invalidVariants[] = $variant->sku;
                continue;
            }

            $categoryIds = $product->category ? [$product->category->id] : [];

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
                $invalidVariants[] = $variant->sku;
            }
        }

        if (!empty($invalidVariants)) {
            return [
                'success' => false,
                'message' => 'Coupon không áp dụng cho các biến thể: ' . implode(', ', $invalidVariants),
                'discount' => 0,
            ];
        }

        if ($subtotal < $voucher->min_amount) {
            return [
                'success' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher->min_amount, 0, ',', '.') . ' ₫',
                'discount' => 0,
            ];
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

        return [
            'success' => true,
            'discount' => $discount,
        ];
    }

    /**
     * Lấy danh sách voucher hợp lệ
     */
    public function getValidVouchers()
    {
        return Voucher::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('quantity', '>', 0)
            ->get()
            ->map(function ($voucher) {
                // Xử lý end_date như chuỗi
                $endDate = Carbon::parse($voucher->end_date)->format('Y-m-d H:i:s');

                return [
                    'name' => $voucher->name,
                    'code' => $voucher->code,
                    'description' => $voucher->description,
                    'end_date' => $endDate,
                    'quantity' => $voucher->quantity,
                    'min_amount' => $voucher->min_amount,
                    'max_discount_amount' => $voucher->max_discount_amount ?? 0,
                    'discount_amount' => $voucher->discount_amount,
                    'type' => $voucher->type,
                    'applies_to' => $voucher->applies_to ?? [],
                ];
            });
    }

    /**
     * Lấy danh sách sản phẩm áp dụng cho voucher
     */
    public function getApplicableProducts($couponCode)
    {
        Log::debug('getApplicableProducts called with couponCode:', [$couponCode]);

        $voucher = Voucher::where('code', $couponCode)->first();
        if (!$voucher) {
            Log::warning('Voucher không tìm thấy:', [$couponCode]);
            return [];
        }

        $appliesTo = $voucher->applies_to ?? [];
        Log::debug('Raw applies_to:', [$appliesTo]);

        // Kiểm tra nếu applies_to là chuỗi JSON
        if (is_string($appliesTo)) {
            Log::warning('applies_to là chuỗi, đang giải mã:', [$appliesTo]);
            $decoded = json_decode($appliesTo, true);
            if (is_array($decoded)) {
                $appliesTo = $decoded;
            } else {
                Log::error('Không thể giải mã chuỗi JSON trong applies_to:', [$appliesTo]);
                return [];
            }
        }

        if (!is_array($appliesTo)) {
            Log::error('applies_to không phải mảng:', [$appliesTo]);
            return [];
        }

        // Xử lý trường hợp applies_to chứa chuỗi JSON
        $processedAppliesTo = [];
        foreach ($appliesTo as $apply) {
            if (is_string($apply) && is_array(json_decode($apply, true))) {
                Log::warning('Tìm thấy chuỗi JSON trong applies_to:', [$apply]);
                $decoded = json_decode($apply, true);
                if (is_array($decoded)) {
                    $processedAppliesTo = array_merge($processedAppliesTo, $decoded);
                } else {
                    Log::warning('Không thể giải mã chuỗi JSON:', [$apply]);
                }
            } else {
                $processedAppliesTo[] = $apply;
            }
        }

        Log::debug('Processed applies_to:', [$processedAppliesTo]);

        $products = [];
        foreach ($processedAppliesTo as $apply) {
            if (is_string($apply) && strpos($apply, 'product_') === 0) {
                $productId = str_replace('product_', '', $apply);
                $product = Product::find($productId);
                if ($product) {
                    $products[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                    ];
                }
            } elseif (is_string($apply) && strpos($apply, 'category_') === 0) {
                $categoryId = str_replace('category_', '', $apply);
                $categoryProducts = Product::whereHas('category', function ($query) use ($categoryId) {
                    $query->where('id', $categoryId);
                })->get();
                foreach ($categoryProducts as $product) {
                    $products[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                    ];
                }
            } else {
                Log::warning('Phần tử applies_to không hợp lệ:', [$apply]);
            }
        }

        return array_values(array_unique($products, SORT_REGULAR));
    }
}
