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
    public function listProduct()
    {
        $products = Product::with(['brand', 'categories'])->get();
        return view('admin.product.index')->with([
            'products' => $products
        ]);
    }

    public function addProduct()
    {
        $brands = Brand::all();
        $variants = Product_variant::all();
        $categories = Category::all();
        $attributes = Attribute::with('values')->get();

        return view('admin.product.add', compact('brands', 'variants', 'categories', 'attributes'));
    }
    public function addPostProduct(ProductRequest $request)
    {
        // Lưu ảnh chính của sản phẩm
        $primaryImagePath = $request->file('image_primary')->store('products', 'public');

        // Kiểm tra xem có id_category trong request hay không, nếu không thì mặc định là [1]
        $idCategory = $request->has('id_category') ? $request->id_category : [1]; // Mặc định là category 1

        // Tạo sản phẩm mới
        $product = Product::create([
            'name' => $request->name,
            'id_brand' => $request->id_brand,
            'description' => $request->description,
            'image_primary' => $primaryImagePath,
            'status' => $request->status,
            'id_category' => $idCategory[0], // Gán category đầu tiên nếu có nhiều category
        ]);

        // Đồng bộ các danh mục với sản phẩm nếu có nhiều danh mục được chọn
        if ($request->has('id_category') && is_array($request->id_category)) {
            $product->categories()->sync($request->id_category);
        }

        // Lưu các biến thể cho sản phẩm
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



        // Trả về thông báo thành công sau khi lưu sản phẩm và các dữ liệu liên quan
        return redirect()->route('admin.products.listProduct')->with('success', 'Thêm sản phẩm thành công');
    }

    public function productDetail()
    {
        return view('client.productDetail');
    }

    public function checkoutProduct()
    {
        return view('client.checkout');
    }
}
