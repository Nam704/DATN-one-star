<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
  public function listVoucher()
  {
    $vouchers = Voucher::all();
    $categories = Category::all()->keyBy('id');
    $products = Product::all()->keyBy('id');

    foreach ($vouchers as $voucher) {
      $applies = $voucher->applies_to ?? [];
      $names = [];
      if (is_array($applies)) {
        foreach ($applies as $item) {
          if (strpos($item, 'category_') === 0) {
            $id = str_replace('category_', '', $item);
            if ($categories->has($id)) {
              $names[] = $categories[$id]->name;
            }
          } elseif (strpos($item, 'product_') === 0) {
            $id = str_replace('product_', '', $item);
            if ($products->has($id)) {
              $names[] = $products[$id]->name;
            }
          }
        }
      }
      $voucher->applies_to_names = implode(', ', $names);
    }

    return view('admin.voucher.index')->with([
      'vouchers' => $vouchers
    ]);
  }

  public function addVoucher()
  {
    $categories = Category::all();
    $products = Product::all();
    return view('admin.voucher.add', compact('categories', 'products'));
  }

  public function addPostVoucher(Request $request)
  {
    $validatedData = $request->validate([
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
          if ($request->type === 'fixed' && $value > 1000000) {
            $fail('Giá trị giảm giá cố định không thể lớn hơn 1,000,000.');
          }
          if ($request->type === 'fixed' && $request->min_amount && $value > $request->min_amount) {
            $fail('Giá trị giảm giá không thể lớn hơn tổng giá trị đơn hàng tối thiểu.');
          }
        }
      ],
      'type' => 'required|in:percentage,fixed',
      'quantity' => 'required|integer|min:1|max:1000',
      'user_limit' => 'required|integer|min:1|lte:quantity|max:10',
      'start_date' => 'required|date|after_or_equal:today',
      'end_date' => 'required|date|after_or_equal:start_date',
      'min_amount' => [
        'nullable',
        'numeric',
        'min:0',
        function ($attribute, $value, $fail) use ($request) {
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
          if ($request->type === 'percentage' && $value > 0) {
            if (!empty($request->min_amount) && $value > ($request->discount_amount * $request->min_amount / 100)) {
              $fail('Giá trị giảm giá tối đa phải nhỏ hơn hoặc bằng mức giảm giá phần trăm.');
            }
          }
        }
      ],
      'status' => 'required|in:active,inactive',
      'applies_to' => 'nullable|array',
      'applies_to.*' => [
        'string',
        function ($attribute, $value, $fail) {
          Log::debug('Validating applies_to item:', [$value]);
          if (!preg_match('/^product_[0-9]+$/', $value) && !preg_match('/^category_[0-9]+$/', $value)) {
            $fail("Giá trị $value không hợp lệ. Phải có định dạng product_[số] hoặc category_[số].");
          }
        },
      ],
    ]);

    // Kiểm tra applies_to trước khi lưu
    $appliesTo = $validatedData['applies_to'] ?? [];
    Log::debug('Saving applies_to:', [$appliesTo]);
    $validatedData['applies_to'] = $appliesTo; // Laravel sẽ tự json_encode

    Voucher::create($validatedData);

    return redirect()->route('admin.vouchers.listVoucher')->with('success', 'Voucher đã được tạo thành công!');
  }

  public function editVoucher($id)
  {
    $voucher = Voucher::findOrFail($id);
    $categories = Category::all();
    $products = Product::all();
    return view('admin.voucher.edit')->with([
      'voucher' => $voucher,
      'categories' => $categories,
      'products' => $products
    ]);
  }

  public function editPutVoucher(Request $request, $id)
  {
    $validatedData = $request->validate([
      'name' => 'required|string|max:255|unique:vouchers,name,' . $id,
      'code' => 'required|string|max:50|unique:vouchers,code,' . $id,
      'description' => 'nullable|string',
      'discount_amount' => [
        'required',
        'numeric',
        'min:0',
        function ($attribute, $value, $fail) use ($request) {
          if ($request->type === 'percentage' && $value > 100) {
            $fail('Giá trị giảm giá theo phần trăm không thể lớn hơn 100%.');
          }
          if ($request->type === 'fixed' && $value > 1000000) {
            $fail('Giá trị giảm giá cố định không thể lớn hơn 1,000,000.');
          }
          if ($request->type === 'fixed' && $request->min_amount && $value > $request->min_amount) {
            $fail('Giá trị giảm giá không thể lớn hơn tổng giá trị đơn hàng tối thiểu.');
          }
        }
      ],
      'type' => 'required|in:percentage,fixed',
      'quantity' => 'required|integer|min:1|max:1000',
      'user_limit' => 'required|integer|min:1|lte:quantity|max:10',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'min_amount' => [
        'nullable',
        'numeric',
        'min:0',
        function ($attribute, $value, $fail) use ($request) {
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
          if ($request->type === 'percentage' && $value > 0) {
            if (!empty($request->min_amount) && $value > ($request->discount_amount * $request->min_amount / 100)) {
              $fail('Giá trị giảm giá tối đa phải nhỏ hơn hoặc bằng mức giảm giá phần trăm.');
            }
          }
        }
      ],
      'status' => 'required|in:active,inactive',
      'applies_to' => 'nullable|array',
      'applies_to.*' => [
        'string',
        function ($attribute, $value, $fail) {
          Log::debug('Validating applies_to item:', [$value]);
          if (!preg_match('/^product_[0-9]+$/', $value) && !preg_match('/^category_[0-9]+$/', $value)) {
            $fail("Giá trị $value không hợp lệ. Phải có định dạng product_[số] hoặc category_[số].");
          }
        },
      ],
    ]);

    $voucher = Voucher::findOrFail($id);
    $appliesTo = $validatedData['applies_to'] ?? [];
    Log::debug('Updating applies_to:', [$appliesTo]);
    $validatedData['applies_to'] = $appliesTo;

    $voucher->update($validatedData);

    return redirect()->route('admin.vouchers.listVoucher')->with('success', 'Cập nhật voucher thành công!');
  }

  public function detailVoucher($id)
  {
    $voucher = Voucher::findOrFail($id);
    $categories = Category::all();
    $products = Product::all();
    return view('admin.voucher.detail')->with([
      'voucher' => $voucher,
      'categories' => $categories,
      'products' => $products
    ]);
  }

  public function deleteVoucher($id)
  {
    $voucher = Voucher::findOrFail($id);
    $voucher->delete();
    return redirect()->route('admin.vouchers.listVoucher')->with('success', 'Xóa voucher thành công!');
  }
}
