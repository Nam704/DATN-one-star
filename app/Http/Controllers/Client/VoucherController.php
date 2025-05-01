<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    // Danh sách voucher
    public function index()
    {
        $vouchers = $this->voucherService->getValidVouchers();
        return view('client.user.index', $vouchers);
    }

    // // Chi tiết voucher
    // public function show($id)
    // {
    //     $voucher = $this->voucherService->getVoucherById($id);

    //     return view('client.vouchers.show', compact('voucher'));
    // }
}

