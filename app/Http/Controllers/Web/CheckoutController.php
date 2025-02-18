<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product_variant;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    // public function index(Request $request)
    // {
    //     // Lấy variant_id và quantity từ query string
    //     $variantId = $request->query('variant_id');
    //     $quantity = $request->query('quantity');

    //     // Kiểm tra nếu không có variant_id hoặc quantity
    //     if (!$variantId || !$quantity) {
    //         return redirect()->back()->with('error', 'Thiếu thông tin biến thể hoặc số lượng.');
    //     }

    //     // Lấy thông tin biến thể từ database
    //     $variant = Product_variant::with(['product', 'product.brand', 'product.category', 'productVariantAttributes.attributeValue.attribute'])
    //         ->find($variantId);

    //     // Kiểm tra nếu biến thể không tồn tại
    //     if (!$variant) {
    //         return redirect()->back()->with('error', 'Biến thể không tồn tại.');
    //     }

    //     // Lấy thông tin sản phẩm
    //     $product = $variant->product;

    //     // Lấy các thuộc tính và giá trị của biến thể
    //     $attributes = [];
    //     foreach ($variant->productVariantAttributes as $pva) {
    //         $attributes[] = [
    //             'attribute_name' => $pva->attributeValue->attribute->name,
    //             'attribute_value' => $pva->attributeValue->value,
    //         ];
    //     }

    //     // Truyền dữ liệu vào view
    //     return view('client.checkout', compact('variant', 'quantity', 'product', 'attributes'));
    // }




    public function index(Request $request)
    {
        $variantId = $request->query('variant_id');
        $quantity = $request->query('quantity');
        if (!$variantId || !$quantity) {
            return redirect()->back()->with('error', 'Thiếu thông tin biến thể hoặc số lượng.');
        }

        $variant = Product_variant::with([
            'product',
            'product.brand',
            'product.category',
            'productVariantAttributes.attributeValue.attribute',
            'images'
        ])->find($variantId);

        if (!$variant) {
            return redirect()->back()->with('error', 'Biến thể không tồn tại.');
        }

        $product = $variant->product;

        $attributes = [];
        foreach ($variant->productVariantAttributes as $pva) {
            $attributes[] = [
                'attribute_name' => $pva->attributeValue->attribute->name,
                'attribute_value' => $pva->attributeValue->value,
            ];
        }


        $images = $variant->images;

        return view('client.checkout', compact('variant', 'quantity', 'product', 'attributes', 'images'));
    }

    // public function store(Request $request)
    // {
    //     $data = $request->all();

    //     $order = [
    //         'id_user' => null,
    //         'phone_number' => $data['phone'],
    //         'address' => $data['address'],
    //         'total_amount' => $data['total_price'],
    //         'id_order_status' => 1,
    //         'id_voucher' => null,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ];

    //     $orderId = DB::table('orders')->insertGetId($order);


    //     $orderDetail = [
    //         'id_order' => $orderId,
    //         'id_product_variant' => $data['selected_variant_id'],
    //         'quantity' => $data['quantity'],
    //         'unit_price' => $data['unit_price'],
    //         'total_price' => $data['total_price'],
    //         'product_name' => $data['name'],
    //         'id_user' => null,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ];


    //     DB::table('order_details')->insert($orderDetail);

    //     return redirect()->route('thankyou');
    // }



    public function store(Request $request)
    {
        $data = $request->all();

        $order = [
            'id_user' => null, 
            'phone_number' => $data['phone'],
            'address' => $data['address'],
            'total_amount' => $data['total_price'],
            'id_order_status' => 1, 
            'id_voucher' => null, 
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $orderId = DB::table('orders')->insertGetId($order);

        $orderDetail = [
            'id_order' => $orderId,
            'id_product_variant' => $data['selected_variant_id'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'total_price' => $data['total_price'],
            'product_name' => $data['name'],
            'id_user' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

       
        DB::table('order_details')->insert($orderDetail);

        $redirectUrl = url('/products/checkout') . '?variant_id=' . $data['selected_variant_id'] . '&quantity=' . $data['quantity'];

        return redirect($redirectUrl)->with('success', 'Đặt hàng thành công!');
    }

}
