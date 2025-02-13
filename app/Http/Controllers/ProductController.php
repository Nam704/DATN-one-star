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

    public function editProduct($id)
{
    $product = Product::with([
        'category',
        'brand',
        'productVariants.productVariantAttributes.attribute',
        'productVariants.productVariantAttributes.attributeValue',
        'images'
    ])->findOrFail($id);
    
    $brands = Brand::all();
    $categories = Category::all();
    $attributes = Attribute::where('status', 'active')->get();

    return view('admin.product.edit', compact(
        'product',
        'brands', 
        'categories',
        'attributes'
    ));
}

public function editPutProduct(Request $request, $id)
{
    $product = Product::findOrFail($id);
    
    // Update basic product info
    $product->update([
        'name' => $request->name,
        'description' => $request->description,
        'id_brand' => $request->id_brand,
        'id_category' => $request->id_category,
        'product_type' => $request->product_type
    ]);

    // Handle image upload if new image
    if ($request->hasFile('image-primary')) {
        // Delete old image
        if ($product->image_primary) {
            Storage::disk('public')->delete($product->image_primary);
        }
        $imagePath = $request->file('image-primary')->store('uploads/products', 'public');
        $product->update(['image_primary' => $imagePath]);
    }

    // Handle product variants if product type is variants
    if ($request->product_type === 'variants') {
        // Update or create variants
        foreach($request->variants as $variantData) {
            $variant = ProductVariant::updateOrCreate(
                ['id_product' => $product->id],
                [
                    'price' => $variantData['price'],
                    'quantity' => $variantData['quantity'],
                    'description' => $variantData['description']
                ]
            );

            // Update variant attributes
            foreach($variantData['attributes'] as $attr) {
                ProductVariantAttribute::updateOrCreate(
                    [
                        'id_product_variant' => $variant->id,
                        'id_attribute' => $attr['attribute_id']
                    ],
                    ['id_attribute_value' => $attr['attribute_value_id']]
                );
            }
        }
    }

    return redirect()->route('admin.products.listProduct')
                    ->with('success', 'Sản phẩm đã được cập nhật thành công.');
}

}
