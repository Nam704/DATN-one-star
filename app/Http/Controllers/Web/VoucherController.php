<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function listVoucher()
    {
      $vouchers = Voucher::all();
       // Lấy tất cả danh mục và sản phẩm (nếu số lượng không quá lớn)
    $categories = Category::all()->keyBy('id'); // mảng key theo id
    $products   = Product::all()->keyBy('id');

    // Với mỗi voucher, chuyển đổi trường applies_to thành tên hiển thị
    foreach ($vouchers as $voucher) {
        // Giả sử dữ liệu được lưu dưới dạng JSON
        $applies = json_decode($voucher->applies_to, true);
        $names   = [];
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
        // Gắn chuỗi tên vào thuộc tính tạm applies_to_names
        $voucher->applies_to_names = implode(', ', $names);
    }
      return view('admin.voucher.index')->with([
        'vouchers' =>$vouchers
      ]);
    }

    public function addVoucher()
    {
      $categories = Category::all(); // Lấy danh sách danh mục
      $products   = Product::all();   // Lấy danh sách sản phẩm (nếu cần
        return view('admin.voucher.add',compact('categories','products'));
    }

    public function addPostVoucher(Request $request)
    {
       $validatedData= $request->validate([
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
            'nullable',
            'numeric',
            'min:0',
            function ($attribute, $value, $fail) use ($request) {
                // Kiểm tra nếu là dạng "percentage" thì `min_amount` bắt buộc phải có
                // if ($request->type === 'percentage' && (!$value || $value <= 0)) {
                //     $fail('Số tiền tối thiểu bắt buộc đối với giảm giá phần trăm.');
                // }

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
                // Chỉ kiểm tra khi loại giảm giá là phần trăm
                if ($request->type === 'percentage' && $value > 0) {
                    // Kiểm tra nếu có min_amount thì mới thực hiện phép tính
                    if (!empty($request->min_amount) && $value < ($request->discount_amount * $request->min_amount / 100)) {
                        $fail('Giá trị giảm giá tối đa phải lớn hơn hoặc bằng mức giảm giá phần trăm.');
                    }
                } 
            }
            ],
            'status' => 'required|in:active,inactive',
            'applies_to'   => 'nullable|array',
            'applies_to.*' => 'string', // Mỗi phần tử của mảng là chuỗi
        ]);
        
        // Chuyển mảng sang JSON
$validatedData['applies_to'] = json_encode($validatedData['applies_to']);
        // Tạo voucher mới
        Voucher::create($validatedData);

        return redirect()->route('admin.vouchers.listVoucher')->with('success', 'Voucher đã được tạo thành công!');
    }

    public function editVoucher($id)
    {
      $vouchers = Voucher::findOrFail($id);
      $vouchers->applies_to = json_decode($vouchers->applies_to, true);

      $categories = Category::all();
      $products   = Product::all();
      return view('admin.voucher.edit')->with([
          'voucher' => $vouchers,
          'categories'=>$categories,
          'products'=>$products
      ]);
    }

    public function editPutVoucher(Request $request, $id)
    {
    $validatedData = $request->validate([
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
        'nullable',
        'numeric',
        'min:0',
        function ($attribute, $value, $fail) use ($request) {
            // Kiểm tra nếu là dạng "percentage" thì `min_amount` bắt buộc phải có
            // if ($request->type === 'percentage' && (!$value || $value <= 0)) {
            //     $fail('Số tiền tối thiểu bắt buộc đối với giảm giá phần trăm.');
            // }

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
            // Chỉ kiểm tra khi loại giảm giá là phần trăm
            if ($request->type === 'percentage' && $value > 0) {
                // Kiểm tra nếu có min_amount thì mới thực hiện phép tính
                if (!empty($request->min_amount) && $value < ($request->discount_amount * $request->min_amount / 100)) {
                    $fail('Giá trị giảm giá tối đa phải lớn hơn hoặc bằng mức giảm giá phần trăm.');
                }
            } 
        }
        ],
        'status' => 'required|in:active,inactive',
        'applies_to'       => 'nullable|array',
        'applies_to.*'     => 'string',
    ]);

    // Lấy voucher theo ID
    $voucher = Voucher::findOrFail($id);
    $validatedData['applies_to'] = json_encode($validatedData['applies_to']);
    // Cập nhật dữ liệu
    $voucher->update(
      $validatedData
      // [
        // 'name' => $request->name,
        // 'code' => $request->code,
        // 'description' => $request->description,
        // 'discount_amount' => $request->discount_amount,
        // 'type' => $request->type,
        // 'quantity' => $request->quantity,
        // 'user_limit' => $request->user_limit,
        // 'start_date' => $request->start_date,
        // 'end_date' => $request->end_date,
        // 'min_amount' => $request->min_amount,
        // 'max_discount_amount' => $request->max_discount_amount,
        // 'status' => $request->status,
        // 'applies_to' => $request->applies_to,
    // ]
  );

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
