<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Product_variant;
use App\Models\Product_variant_attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['brand', 'category'])->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();
        $attributes = Attribute::with('attributeValues')
            ->where('status', 'active')
            ->get();
        return view('admin.products.create', compact('brands', 'categories', 'attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_brand' => 'required|exists:brands,id',
            'id_category' => 'required|exists:categories,id',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'base_stock' => 'required|integer|min:0',
            'image_primary' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = 'products/default.jpg'; // Default image path

        if ($request->hasFile('image_primary') && $request->file('image_primary')->isValid()) {
            $imagePath = $request->file('image_primary')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $validated['name'],
            'id_brand' => $validated['id_brand'],
            'id_category' => $validated['id_category'],
            'description' => $validated['description'],
            'image_primary' => $imagePath,
            'status' => 'active'
        ]);

        if ($request->has('attributes')) {
            $combinations = $this->generateVariantCombinations($request->attribute_values);

            foreach ($combinations as $combination) {
                $variant = Product_variant::create([
                    'id_product' => $product->id,
                    'sku' => 'SKU-' . uniqid(),
                    'price' => $validated['base_price'],
                    'stock' => $validated['base_stock'],
                    'status' => 'active'
                ]);

                foreach ($combination as $attributeId => $valueId) {
                    Product_variant_attribute::create([
                        'id_product_variant' => $variant->id,
                        'id_attribute_value' => $valueId
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = Product::with(['variants.attributes'])->findOrFail($id);
        $brands = Brand::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();
        $attributes = Attribute::with('attributeValues')
            ->where('status', 'active')
            ->get();
        return view('admin.products.edit', compact('product', 'brands', 'categories', 'attributes'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_brand' => 'required|exists:brands,id',
            'id_category' => 'required|exists:categories,id',
            'description' => 'required|string',
            'status' => 'required|in:active,inactive',
            'image_primary' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image_primary') && $request->file('image_primary')->isValid()) {
            if ($product->image_primary && $product->image_primary !== 'products/default.jpg') {
                Storage::disk('public')->delete($product->image_primary);
            }
            $validated['image_primary'] = $request->file('image_primary')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image_primary && $product->image_primary !== 'products/default.jpg') {
            Storage::disk('public')->delete($product->image_primary);
        }

        $product->delete();

        return response()->json(['success' => true]);
    }

    public function trash()
    {
        $trashedProducts = Product::with(['brand', 'category'])
            ->onlyTrashed()
            ->get();
        return view('admin.products.trash', compact('trashedProducts'));
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        return response()->json(['success' => true]);
    }

    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        if ($product->image_primary && $product->image_primary !== 'products/default.jpg') {
            Storage::disk('public')->delete($product->image_primary);
        }

        $product->forceDelete();

        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();
        return response()->json(['success' => true]);
    }

    private function generateVariantCombinations($attributeValues)
    {
        $result = [[]];

        foreach ($attributeValues as $attributeId => $values) {
            $tmp = [];
            foreach ($result as $combination) {
                foreach ($values as $valueId) {
                    $tmp[] = $combination + [$attributeId => $valueId];
                }
            }
            $result = $tmp;
        }

        return $result;
    }
}
