<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Product_variant;
use App\Models\Product_variant_attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['brand', 'category'])->get();
        return view('admin.product.index')->with([
            'products' => $products
        ]);
    }

    public function create()
    {
        $brands = Brand::all();
        $variants = Product_variant::all();
        $categories = Category::all();
        $attributes = Attribute::with('values')->get();

        return view('admin.product.create', compact('brands', 'categories', 'attributes'));
    }

    /**
     * Lưu sản phẩm mới vào cơ sở dữ liệu.
     */
    // App\Http\Controllers\Web\ProductController.php

    public function store(Request $request)
    {
        // Validate và tạo sản phẩm
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_brand' => 'required|exists:brands,id',
            'id_category' => 'required|exists:categories,id',
            'description' => 'required|string',
            'image_primary' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants.*.sku' => 'required|string|distinct|unique:product_variants,sku',
            'variants.*.status' => 'required|boolean',
        ]);

        // Upload ảnh chính
        $primaryImagePath = $request->file('image_primary')->store('products', 'public');

        // Tạo sản phẩm
        $product = Product::create([
            'name' => $validated['name'],
            'id_brand' => $validated['id_brand'],
            'id_category' => $validated['id_category'],
            'description' => $validated['description'],
            'image_primary' => $primaryImagePath,
        ]);

        // Thêm các biến thể
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                // Tạo một biến thể và liên kết với sản phẩm
                $productVariant = $product->variants()->create([
                    'sku' => $variant['sku'],
                    'status' => $variant['status'],
                ]);

                // Lúc này, $productVariant->id chính là id_product_variant của biến thể mới tạo
            }
        }

        // Thêm thuộc tính
        if ($request->has('attributes')) {
            foreach ($request->attributes as $attributeId => $values) {
                foreach ($values as $valueId) {
                    $product->attributeValues()->attach($valueId);
                }
            }
        }

        // Lưu ảnh phụ và gắn với biến thể đúng
        if ($request->hasFile('additional_images')) {
            // Giả sử muốn gắn tất cả ảnh vào biến thể đầu tiên
            $variantId = $product->variants->first()->id ?? null;

            if (!$variantId) {
                throw new \Exception('Không tìm thấy ID biến thể để gắn ảnh.');
            }

            foreach ($request->file('additional_images') as $file) {
                $path = $file->store('products', 'public');

                $product->images()->create([
                    'url' => $path,
                    'id_product_variant' => $variantId, // Gắn tất cả ảnh vào ID biến thể
                ]);
            }
        }


        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }
}
