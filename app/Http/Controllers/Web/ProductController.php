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
        DB::beginTransaction(); // Bắt đầu transaction để đảm bảo dữ liệu đồng bộ nếu có lỗi xảy ra
        try {
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
                foreach ($request->variants as $variantIndex => $variantData) {
                    // Tạo biến thể và liên kết với sản phẩm
                    $productVariant = $product->variants()->create([
                        'sku' => $variantData['sku'],
                        'status' => $variantData['status'] === 'active', // Chuyển "active" thành true và "inactive" thành false
                    ]);

                    // Lưu ảnh cho từng biến thể
                    if (isset($variantData['images'])) {
                        foreach ($variantData['images'] as $key => $imageFile) {
                            if ($key >= 4) break; // Giới hạn tối đa 4 ảnh
                            $path = $imageFile->store('images', 'public');
                            $product->images()->create([
                                'url' => $path,
                                'id_product_variant' => $productVariant->id, // Gắn ảnh vào biến thể cụ thể
                            ]);
                        }
                    }
                }
            }


            DB::commit(); // Commit transaction
            // Trả về thông báo thành công
            return redirect()->route('admin.products.listProduct')->with('success', 'Thêm sản phẩm thành công');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback nếu có lỗi xảy ra
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
