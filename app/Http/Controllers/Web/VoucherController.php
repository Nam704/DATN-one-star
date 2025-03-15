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
            'name' => 'required|string|max:255|unique:vouchers,name',
            'code' => 'required|string|max:50|unique:vouchers,code',
            'description' => 'nullable|string',
            'discount_amount' => [
              'required',
              'numeric',
              'min:0',
              function ($attribute, $value, $fail) use ($request) {
                  if ($request->type === 'percentage' && $value > 100) {
                      $fail('Giá trị giảm giá theo phần trăm không thể lớn hơn 100%.');
                  }
                  if ($request->type === 'fixed' && $value > 1000000) { // Giới hạn fixed tối đa
                    $fail('Giá trị giảm giá cố định không thể lớn hơn 1,000,000.');
                  }
                  if ($request->type === 'fixed' && $request->min_amount && $value > $request->min_amount) {
                    $fail('Giá trị giảm giá không thể lớn hơn tổng giá trị đơn hàng tối thiểu.');
                  }
              }
          ],
            'type' => 'required|in:percentage,fixed',
            'quantity' => 'required|integer|min:1',
            'user_limit' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min_amount' => [
            'numeric',
            'min:0',
            function ($attribute, $value, $fail) use ($request) {
                // Kiểm tra nếu là dạng "percentage" thì `min_amount` bắt buộc phải có
                if ($request->type === 'percentage' && (!$value || $value <= 0)) {
                    $fail('Số tiền tối thiểu bắt buộc đối với giảm giá phần trăm.');
                }

                // Kiểm tra nếu là dạng "fixed", `min_amount` không thể nhỏ hơn `discount_amount`
                if ($request->type === 'fixed' && $value !== null && $value < $request->discount_amount) {
                    $fail('Số tiền tối thiểu không thể nhỏ hơn giá trị giảm giá.');
                }
            }
        ],
            'max_discount_amount' => [
              'nullable',
              'numeric',
              'min:0',
              function ($attribute, $value, $fail) use ($request) {
                if ($request->type === 'percentage' && $value > 0 && $value < ($request->discount_amount * $request->min_amount / 100)) {
                    $fail('Giá trị giảm giá tối đa phải nhỏ hơn hoặc bằng mức giảm giá phần trăm.');
                }
            }
            ],
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
      return view('admin.voucher.edit')->with([
          'voucher' => $vouchers,
      ]);
    }

    public function editPutVoucher(Request $request, $id)
    {
      $request->validate([
        'name' => 'required|string|max:255|unique:vouchers,name,' .$id,
        'code' => 'required|string|max:50|unique:vouchers,code,' .$id,
        'description' => 'nullable|string',
        'discount_amount' => [
          'required',
          'numeric',
          'min:0',
          function ($attribute, $value, $fail) use ($request) {
              if ($request->type === 'percentage' && $value > 100) {
                  $fail('Giá trị giảm giá theo phần trăm không thể lớn hơn 100%.');
              }
              if ($request->type === 'fixed' && $value > 1000000) { // Giới hạn fixed tối đa
                $fail('Giá trị giảm giá cố định không thể lớn hơn 1,000,000.');
              }
              if ($request->type === 'fixed' && $request->min_amount && $value > $request->min_amount) {
                $fail('Giá trị giảm giá không thể lớn hơn tổng giá trị đơn hàng tối thiểu.');
              }
          }
      ],
        'type' => 'required|in:percentage,fixed',
        'quantity' => 'required|integer|min:1',
        'user_limit' => 'required|integer|min:1',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'min_amount' => [
        'numeric',
        'min:0',
        function ($attribute, $value, $fail) use ($request) {
            // Kiểm tra nếu là dạng "percentage" thì `min_amount` bắt buộc phải có
            if ($request->type === 'percentage' && (!$value || $value <= 0)) {
                $fail('Số tiền tối thiểu bắt buộc đối với giảm giá phần trăm.');
            }

            // Kiểm tra nếu là dạng "fixed", `min_amount` không thể nhỏ hơn `discount_amount`
            if ($request->type === 'fixed' && $value !== null && $value < $request->discount_amount) {
                $fail('Số tiền tối thiểu không thể nhỏ hơn giá trị giảm giá.');
            }
        }
    ],
        'max_discount_amount' => [
          'nullable',
          'numeric',
          'min:0',
          function ($attribute, $value, $fail) use ($request) {
            if ($request->type === 'percentage' && $value > 0 && $value < ($request->discount_amount * $request->min_amount / 100)) {
                $fail('Giá trị giảm giá tối đa phải nhỏ hơn hoặc bằng mức giảm giá phần trăm.');
            }
        }
        ],
        'status' => 'required|in:active,inactive',
        'applies_to' => 'nullable|string',
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
