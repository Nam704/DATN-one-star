<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\Product_variant;
use App\Models\Product_variant_attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function create()
    {
        $attributes = Attribute::where('status', 'active')->get();
        return view('admin.products.create', compact('attributes'));
    }

    public function store(Request $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'status' => 'active'
        ]);

        $variant = Product_variant::create([
            'id_product' => $product->id,
            'sku' => 'SKU-' . uniqid(),
            'status' => 'active'
        ]);

        foreach($request->attributes as $key => $attributeId) {
            if($attributeId && $request->attribute_values[$key]) {
                $attributeValue = AttributeValue::create([
                    'id_attribute' => $attributeId,
                    'value' => $request->attribute_values[$key],
                    'status' => 'active'
                ]);

                Product_variant_attribute::create([
                    'id_prouct_variant' => $variant->id,
                    'id_attribute_value' => $attributeValue->id
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }
}
