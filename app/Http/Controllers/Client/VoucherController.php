<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{

    public function index()
    {
        // Lấy danh sách voucher từ cơ sở dữ liệu (nếu cần)
        $vouchers = Voucher::where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();
    
        // Truyền dữ liệu vào view
        return view('client.user.index', compact('vouchers'));
    }
    
    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('client.user.voucher', compact('voucher'));
    }

}
