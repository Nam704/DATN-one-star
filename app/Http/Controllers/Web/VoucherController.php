<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function listVoucher()
    {
      $vouchers = Voucher::all();
      return view('admin.voucher.index')->with([
        'vouchers' =>$vouchers
      ]);
    }

    public function addVoucher()
    {
        return view('admin.voucher.add');
    }

    public function addPostVoucher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:vouchers,code',
            'description' => 'nullable|string',
            'discount_amount' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'quantity' => 'required|integer|min:1',
            'user_limit' => 'required|integer|min:1',
            'total_usage' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'applies_to' => 'nullable|string',
        ]);

        // Tạo voucher mới
        Voucher::create($request->all());

        return redirect()->route('admin.vouchers.listVoucher')->with('success', 'Voucher đã được tạo thành công!');
    }

    public function editVoucher($id)
    {
      $vouchers = Voucher::findOrFail($id);

      $category_parent=Voucher::all();
      return view('admin.voucher.edit')->with([
          'voucher' => $vouchers,
      ]);
    }

    public function editPutVoucher(Request $request, $id)
    {
      $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:vouchers,code,' . $id,
        'description' => 'nullable|string',
        'discount_amount' => 'required|numeric|min:0',
        'type' => 'required|in:percentage,fixed',
        'quantity' => 'required|integer|min:1',
        'user_limit' => 'required|integer|min:1',
        'total_usage' => 'nullable|integer|min:0',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'min_amount' => 'nullable|numeric|min:0',
        'max_discount_amount' => 'nullable|numeric|min:0',
        'status' => 'required|in:active,inactive',
        'applies_to' => 'nullable|string|max:255',
    ]);

    // Lấy voucher theo ID
    $voucher = Voucher::findOrFail($id);

    // Cập nhật dữ liệu
    $voucher->update([
        'name' => $request->name,
        'code' => $request->code,
        'description' => $request->description,
        'discount_amount' => $request->discount_amount,
        'type' => $request->type,
        'quantity' => $request->quantity,
        'user_limit' => $request->user_limit,
        'total_usage' => $request->total_usage ?? 0, // Mặc định 0 nếu không nhập
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'min_amount' => $request->min_amount,
        'max_discount_amount' => $request->max_discount_amount,
        'status' => $request->status,
        'applies_to' => $request->applies_to,
    ]);

    // Chuyển hướng với thông báo thành công
    return redirect()->route('admin.vouchers.listVoucher')->with('success', 'Cập nhật voucher thành công!');

    }

    public function deleteVoucher($id)
    {
          $voucher = Voucher::findOrFail($id);
          $voucher->delete();
          return redirect()->route('admin.vouchers.listVoucher')->with('success', 'Xóa voucher thành công!');
    }
}
