<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function check(Request $request)
{
    $voucher = Voucher::where('code', $request->voucher_code)
        ->where('quantity', '>', 0)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->first();

    if (!$voucher) {
        return response()->json([
            'success' => false,
            'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
        ]);
    }

    return response()->json([
        'success' => true,
        'discount' => $voucher->discount_amount,
        'message' => 'Áp dụng mã giảm giá thành công'
    ]);
}
}
