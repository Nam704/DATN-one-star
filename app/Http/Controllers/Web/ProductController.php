<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Product_variant;
use App\Models\Product_variant_attribute;
use App\Models\Image;

use App\Models\Category_product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function listProduct()
    {
        $products = Product::with(['category'])->get();
        ;
        return view('admin.product.index')->with([
            'products' => $products
        ]);
    }

    public function addProduct()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.product.add')->with(
            [
                'categories' => $categories,
                'brands' => $brands
            ]
        );
    }


    public function addPostProduct(Request $request)
    {
        echo '<pre>';
        var_dump($request->all());
        echo '</pre>';

        // Xử lý ảnh chính
        if ($request->hasFile('image-primary')) {
            $image = $request->file('image-primary');
            if ($image->isValid()) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('uploads/products', $imageName, 'public'); // Lưu ảnh vào storage/app/public/uploads/products/
                $imageUrl = str_replace('storage/', '', Storage::url($path));
                $imageUrl = url('storage/uploads/products/' . $imageName);
            } else {
                return response()->json(['error' => 'Invalid image'], 400);
            }
        } else {
            $imageUrl = '';
        }

        // Tạo sản phẩm
        $newProductData = [
            'product_type' => $request->product_type,
            'name' => $request->name,
            'description' => $request->description,
            'image_primary' => $imageUrl,
            'id_brand' => $request->id_brand,
        ];

        // Kiểm tra loại sản phẩm 'simple' và gán giá
        if ($request->product_type == 'simple') {
            $newProductData['price'] = $request->price ?? 0;
            $newProductData['price_sale'] = $request->price_sale ?? 0;
        }

        $product = Product::create($newProductData);


        // xử lý ảnh pgụ
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $imageName = time() . '_' . $image->getClientOriginalName();


                    $path = $image->storeAs('uploads/products', $imageName, 'public');
                    $imageUrl = str_replace('storage/', '', Storage::url($path));
                    Image::create([
                        'product_id' => $product->id,
                        'url' => $imageUrl,
                        'status' => 'active'
                    ]);
                } else {
                    return response()->json(['error' => 'Invalid image'], 400);
                }
            }
        } else {
            return response()->json(['error' => 'No images provided'], 400);
        }


        // Xử lý danh mục cho sản phẩm
        if (!empty($request->categories)) {
            foreach ($request->categories as $categoryId) {
                Category_product::create([
                    'product_id' => $product->id,
                    'category_id' => $categoryId,
                ]);
            }
        }

        // Xử lý sản phẩm có biến thể
        if ($request->product_type == 'variants') {

            // Kiểm tra tổng số biến thể
            for ($i = 0; $i < $request->total_variant; $i++) {
                if (
                    isset($request->price_product_variant[$i]) &&
                    isset($request->quantity_product_variant[$i]) &&
                    isset($request->description_product_variant[$i])
                ) {

                    // Xử lý ảnh cho biến thể
                    $imageUrl = '';
                    if ($request->hasFile('image_product_variant') && isset($request->image_product_variant[$i])) {
                        $image = $request->file('image_product_variant')[$i];
                        if ($image->isValid()) {
                            $imageName = time() . '_' . $image->getClientOriginalName();
                            $path = $image->storeAs('uploads/products', $imageName, 'public');
                            $imageUrl = str_replace('storage/', '', Storage::url($path));
                            $imageUrl = url('storage/uploads/products/' . $imageName);
                        }
                    }
 
                    // Tạo sản phẩm biến thểe
                    $product_variant = Product_variant::create([
                        'id_product' => $product->id,
                        'sku' => 'test' . $i,
                        'quantity' => $request->quantity_product_variant[$i],
                        'price' => $request->price_product_variant[$i],
                        'description' => $request->description_product_variant[$i],
                        'image' => $imageUrl,
                    ]);

                    // Xử lý biến thể (attributes)
                    if (isset($request->variants[$i])) {
                        $Configurations = json_decode($request->variants[$i]);

                        // Kiểm tra nếu json_decode thành công và trả về một mảng
                        if (is_array($Configurations)) {
                            foreach ($Configurations as $config) {

                                if (isset($config->attribute_id) && isset($config->attribute_value_id)) {

                                    Product_variant_attribute::create([
                                        'id_product_variant' => $product_variant->id,
                                        'id_attribute' => $config->attribute_id,
                                        'id_attribute_value' => $config->attribute_value_id,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
        return redirect()->route('admin.products.listProduct')->with('success', 'Sản phẩm đã được thêm thành công!');
    }


    public function editProduct($id)
    {
        $brands = Brand::all();
        $categories = Category::all();
        $products = Product::findOrFail($id);

        return view('admin.product.edit')->with([
            'brands' => $brands,
            'categories' => $categories,
            'product' => $products
        ]);
    }

    public function editPutProduct(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'id_brand' => 'required|exists:brands,id',
            'id_category' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image_primary' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status' => 'required|in:active,inactive',
        ]);

        $product = Product::findOrFail($id);
        $product->fill($request->except('image_primary'));

        $imagePath = null;
        if ($request->hasFile('image_primary')) {
            if ($product->image_primary && Storage::disk('public')->exists($product->image_primary)) {
                Storage::disk('public')->delete($product->image_primary);
            }
            $imagePath = $request->file('image_primary')->store('uploads/products', 'public');
        } else {
            $imagePath = $product->image_primary; // Giữ lại hình ảnh hiện tại nếu không có hình ảnh mới
        }

        $product->update([
            'name' => $request->name,
            'id_brand' => $request->id_brand,
            'id_category' => $request->id_category,
            'description' => $request->description,
            'image_primary' => $imagePath,
            'status' => $request->status

        ]);


        return redirect()->route('admin.products.listProduct')->with('success', 'Sản phẩm đã được cập nhật.');
    }

    public function deleteProduct($id)
    {
        $products = Product::findOrFail($id);
        if ($products->image_primary && Storage::disk('public')->exists($products->image_primary)) {
            Storage::disk('public')->delete($products->image_primary);
        }
        $products->delete();
        return redirect()->route('admin.products.listProduct')->with('success', 'Xóa thành công');
    }
}
