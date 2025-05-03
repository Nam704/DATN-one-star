<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Lấy tất cả voucher hợp lệ
     */
    public function getValidVouchers(): JsonResponse
    {
        try {
            $vouchers = $this->voucherService->getValidVouchers();
            return response()->json([
                'success' => true,
                'data' => $vouchers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách voucher',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Lấy danh sách sản phẩm áp dụng cho voucher
     */
    public function getApplicableProducts($code): JsonResponse
    {
        try {
            $products = $this->voucherService->getApplicableProducts($code);
            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách sản phẩm',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Áp dụng coupon
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'coupon' => 'required|string',
                'variants' => 'required|array',
                'subtotal' => 'required|numeric|min:0',
            ]);

            $result = $this->voucherService->applyCoupon(
                $validated['coupon'],
                $validated['variants'],
                $validated['subtotal']
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi áp dụng coupon',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
