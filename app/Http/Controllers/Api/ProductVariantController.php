<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product_variant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    protected $product_variant;
    function __construct(Product_variant $product_variant)
    {
        $this->product_variant = $product_variant;
    }
    public function getProductVariants($idProduct)
    {
        $variants = Product_variant::list($idProduct)->get();

        return response()->json($variants);
    }
    function total($idProduct)
    {
        $total =  $this->product_variant->total($idProduct)->count();
        return response()->json(['total' => $total]);
    }
    public function show($id)
    {
        $variant = Product_variant::with('attributeValues')->find($id);

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Biến thể không tồn tại',
            ], 404);
        }

        // Chuẩn bị dữ liệu trả về
        $data = [
            'id' => $variant->id,
            'sku' => $variant->sku,
            'attributeValues' => $variant->attributeValues->map(function ($attr) {
                return [
                    'attribute_name' => $attr->attribute_name,
                    'value' => $attr->value,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
