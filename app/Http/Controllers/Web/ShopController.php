<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Hiển thị trang shop với danh sách sản phẩm và các bộ lọc
     */
    public function shop(Request $request)
    {
        // Lấy danh mục cha (và các danh mục con của chúng) và thương hiệu đang active
        $categories = Category::where(function ($query) {
            $query->whereNull('id_parent')
                  ->orWhere('id_parent', 0);
        })->with('children')->get();
        $brands = Brand::where('status', 'active')->get();

        // Xác định giá tối đa mặc định (loại bỏ dấu chấm nếu có)
        $maxPrice = (int) str_replace('.', '', $request->input('max_price', 50000000));

        // Khởi tạo query sản phẩm với điều kiện sản phẩm active
        $productsQuery = Product::where('status', 'active');

        // --- Lọc theo danh mục ---
        $selectedCategories = $request->input('categories', []);
        if (!is_array($selectedCategories)) {
            // Nếu nhận về dạng chuỗi (các id phân tách bởi dấu phẩy)
            $selectedCategories = explode(',', $selectedCategories);
        }
        if (!empty($selectedCategories)) {
            // Lấy tất cả ID danh mục được chọn và danh mục con của nó
            $allCategoryIds = Category::whereIn('id', $selectedCategories)
                ->orWhereIn('id_parent', $selectedCategories)
                ->pluck('id')
                ->toArray();
            $productsQuery->whereIn('id_category', $allCategoryIds);
        }

        // --- Lọc theo thương hiệu ---
        $selectedBrands = $request->input('brand', $request->input('brands', []));
        if (!is_array($selectedBrands)) {
            $selectedBrands = explode(',', $selectedBrands);
        }
        if (!empty($selectedBrands)) {
            $productsQuery->whereIn('id_brand', $selectedBrands);
        }

        // --- Lọc theo khoảng giá dựa trên expected_price trong import_details (nếu cần) ---
        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = (float)$request->input('min_price', 0);
            $maxPriceInput = (float)$request->input('max_price', $maxPrice);
            $productsQuery->whereHas('variants.importDetails', function ($query) use ($minPrice, $maxPriceInput) {
                $query->whereBetween('expected_price', [$minPrice, $maxPriceInput]);
            });
        }

        // --- Lọc theo từ khóa tìm kiếm (ví dụ: "iphone") ---
        $search = $request->input('search', '');
        if (!empty($search)) {
            // Điều kiện tìm kiếm sản phẩm có tên chứa chuỗi nhập vào
            $productsQuery->where('name', 'like', '%' . $search . '%');
        }

        // Lấy sản phẩm kèm quan hệ cần thiết và phân trang (ví dụ: 9 sản phẩm/trang)
        $products = $productsQuery->with(['variants.importDetails'])->paginate(9);

        // Tính toán giá tối thiểu cho mỗi sản phẩm từ các giá của variants
        $products->getCollection()->transform(function ($product) {
            $prices = $product->variants->pluck('price')->toArray();
            $product->min_price = !empty($prices) ? min($prices) : 0;
            return $product;
        });

        // Trả về view kèm theo dữ liệu danh mục, thương hiệu, sản phẩm và giá tối đa
        return view('client.shops.shop', compact('categories', 'brands', 'products', 'maxPrice'));
    }

    /**
     * Xử lý filter theo AJAX cho trang shop
     */
    public function filter(Request $request)
    {
        $orderBy = $request->input('orderby', 'default');
        $categories = $request->input('categories', []);
        $brands = $request->input('brands', []);
        $minPrice = (float)$request->input('min_price', 0);
        $maxPrice = (float)$request->input('max_price', 50000000);
        $search = $request->input('search', '');

        // Khởi tạo query sản phẩm có trạng thái active
        $productsQuery = Product::where('status', 'active');

        // --- Lọc theo danh mục ---
        if (!empty($categories)) {
            if (is_string($categories)) {
                $categories = explode(',', $categories);
            }
            $productsQuery->whereIn('id_category', $categories);
        }

        // --- Lọc theo thương hiệu ---
        $selectedBrands = $request->input('brand', $brands);
        if (!is_array($selectedBrands)) {
            $selectedBrands = explode(',', $selectedBrands);
        }
        if (!empty($selectedBrands)) {
            $productsQuery->whereIn('id_brand', $selectedBrands);
        }

        // --- Lọc theo khoảng giá dựa trên trường price trong bảng product_variants ---
        if ($minPrice != 0 || $maxPrice != 50000000) {
            $productsQuery->whereHas('variants', function ($query) use ($minPrice, $maxPrice) {
                $query->whereBetween('price', [$minPrice, $maxPrice]);
            });
        }

        // --- Lọc theo từ khóa tìm kiếm ---
        if (!empty($search)) {
            $productsQuery->where('name', 'like', '%' . $search . '%');
        }

        // --- Sắp xếp sản phẩm theo yêu cầu ---
        switch ($orderBy) {
            case 'price_asc': // Giá từ thấp đến cao
                $productsQuery->orderByRaw('(
                    SELECT MIN(pv.price)
                    FROM product_variants as pv
                    WHERE pv.id_product = products.id
                ) ASC');
                break;
            case 'price_desc': // Giá từ cao đến thấp
                $productsQuery->orderByRaw('(
                    SELECT MIN(pv.price)
                    FROM product_variants as pv
                    WHERE pv.id_product = products.id
                ) DESC');
                break;
            case 'name_asc': // Tên A → Z
                $productsQuery->orderBy('name', 'asc');
                break;
            case 'name_desc': // Tên Z → A
                $productsQuery->orderBy('name', 'desc');
                break;
            default:
                $productsQuery->orderBy('created_at', 'desc');
                break;
        }

        // Lấy sản phẩm kèm quan hệ và phân trang
        $products = $productsQuery->with('variants')->paginate(9);

        // Tính toán giá tối thiểu cho mỗi sản phẩm
        $products->getCollection()->transform(function ($product) {
            $prices = $product->variants->pluck('price')->toArray();
            $product->min_price = !empty($prices) ? min($prices) : 0;
            return $product;
        });

        // Render HTML danh sách sản phẩm và phân trang từ các view tương ứng
        $productsHtml = view('client.shops.product-list', compact('products'))->render();
        $paginationHtml = view('client.shops.pagination', compact('products'))->render();

        // Trả về kết quả dạng JSON để cập nhật AJAX trên trang shop
        return response()->json([
            'products'   => $productsHtml,
            'pagination' => $paginationHtml,
        ]);
    }
}
