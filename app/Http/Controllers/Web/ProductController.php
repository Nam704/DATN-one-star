<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\Product_variant;
use App\Models\Product_variant_attribute;
use App\Models\Attribute_value;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $attributes = Attribute::where('status', 'active')->get();
        return view('admin.products.create', compact('attributes'));
    }

    public function store(Request $request)
{
    $product = Product::create([
        'name' => $request->name,
        'id_brand' => 1,
        'id_category' => 1,
        'description' => 'Default description',
        'image_primary' => 'default.jpg',
        'status' => 'active'
    ]);

    $variant = Product_variant::create([
        'id_product' => $product->id,
        'sku' => 'SKU-' . uniqid(),
        'status' => 'active'
    ]);

    if ($request->input('attributes.0') && $request->input('attribute_values.0')) {
        $attributeValue = Attribute_value::create([
            'id_attribute' => $request->input('attributes.0'),
            'value' => $request->input('attribute_values.0'),
            'status' => 'active'
        ]);

        Product_variant_attribute::create([
            'id_prouct_variant' => $variant->id,
            'id_attribute_value' => $attributeValue->id
        ]);
    }

    if ($request->new_attribute && $request->new_attribute_value) {
        $attribute = Attribute::create([
            'name' => $request->new_attribute,
            'status' => 'active'
        ]);

        $attributeValue = Attribute_value::create([
            'id_attribute' => $attribute->id,
            'value' => $request->new_attribute_value,
            'status' => 'active'
        ]);

        Product_variant_attribute::create([
            'id_prouct_variant' => $variant->id,
            'id_attribute_value' => $attributeValue->id
        ]);
    }

    return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
}


}
