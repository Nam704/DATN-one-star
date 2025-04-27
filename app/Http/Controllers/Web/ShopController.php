<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Import_detail;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function shop(Request $request)
    {
        // Fetch categories and brands
        $categories = Category::where(function ($query) {
            $query->whereNull('id_parent')
                ->orWhere('id_parent', 0);
        })->with('children')->get();
        $brands = Brand::where('status', 'active')->get();

        // Determine the maximum price
        $maxPrice = (int) str_replace('.', '', $request->input('max_price', 50000000));

        // Initialize the product query
        $productsQuery = Product::where('status', 'active');

        $banners = Banner::where('status', 1)
        ->where(function ($query) {
            $query->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
        })
        ->where(function ($query) {
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
        })
        ->orderBy('created_at', 'desc')
        ->get();

        // Apply category filters if present
        // Lấy tham số 'categories' và ép thành mảng
        $selectedCategories = $request->input('categories', []);
        if (!is_array($selectedCategories)) {
            // Nếu là chuỗi, giả sử các id được phân tách bởi dấu phẩy
            $selectedCategories = explode(',', $selectedCategories);
        }

        if (!empty($selectedCategories)) {
            // Lấy danh sách các ID của danh mục được chọn và các danh mục con của nó
            $allCategoryIds = Category::whereIn('id', $selectedCategories)
                ->orWhereIn('id_parent', $selectedCategories)
                ->pluck('id')
                ->toArray();

            $productsQuery->whereIn('id_category', $allCategoryIds);
        }


        $selectedBrands = $request->input('brand', $request->input('brands', []));
        if (!is_array($selectedBrands)) {
            // Nếu là chuỗi, giả sử các id được phân tách bởi dấu phẩy
            $selectedBrands = explode(',', $selectedBrands);
        }

        if (!empty($selectedBrands)) {
            $productsQuery->whereIn('id_brand', $selectedBrands);
        }

        // Apply price filter based on expected_price from import_details (nếu cần)
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

        // Fetch paginated products with relations
        $products = $productsQuery->with(['variants.importDetails'])->paginate(9);

        // Tính toán giá tối thiểu cho mỗi sản phẩm (chỉ hiển thị min_price)
        $products->getCollection()->transform(function ($product) {
            $prices = $product->variants->pluck('price')->toArray();
            $product->min_price = !empty($prices) ? min($prices) : 0;
            return $product;
        });

        // Return view with data
        return view('client.shops.shop', compact('categories', 'brands', 'products', 'maxPrice','banners'));
    }

    public function filter(Request $request)
    {
        // Lấy các tham số lọc từ request
        $orderBy = $request->input('orderby', 'default');
        $categories = $request->input('categories', []);
        $brands = $request->input('brands', []);
        $minPrice = (float)$request->input('min_price', 0);
        $maxPrice = (float)$request->input('max_price', 50000000);
        $search = $request->input('search', '');

        // Khởi tạo truy vấn sản phẩm
        $productsQuery = Product::where('status', 'active');

        // Lọc theo danh mục
        if (!empty($categories)) {
            if (is_string($categories)) {
                $categories = explode(',', $categories);
            }
            $productsQuery->whereIn('id_category', $categories);
        }

        // Lọc theo thương hiệu
        $selectedBrands = $request->input('brand', $request->input('brands', []));
        if (!is_array($selectedBrands)) {
            // Nếu là chuỗi, giả sử các id được phân tách bởi dấu phẩy
            $selectedBrands = explode(',', $selectedBrands);
        }

        if (!empty($selectedBrands)) {
            $productsQuery->whereIn('id_brand', $selectedBrands);
        }

        // Lọc theo khoảng giá dựa trên bảng product_variants (trường price)
        if ($minPrice != 0 || $maxPrice != 50000000) {
            $productsQuery->whereHas('variants', function ($query) use ($minPrice, $maxPrice) {
                $query->whereBetween('price', [$minPrice, $maxPrice]);
            });
        }

        // Lọc theo tên sản phẩm nếu có tham số 'search'
        if (!empty($search)) {
            $productsQuery->where('name', 'like', '%' . $search . '%');
        }

        // Sắp xếp sản phẩm theo yêu cầu của người dùng
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

        // Lấy sản phẩm với phân trang (ví dụ 9 sản phẩm/trang) và load quan hệ
        $products = $productsQuery->with('variants')->paginate(9);

        // Tính toán giá tối thiểu cho mỗi sản phẩm
        $products->getCollection()->transform(function ($product) {
            $prices = $product->variants->pluck('price')->toArray();
            $product->min_price = !empty($prices) ? min($prices) : 0;
            return $product;
        });

        // Render view thành HTML
        $productsHtml = view('client.shops.product-list', compact('products'))->render();
        $paginationHtml = view('client.shops.pagination', compact('products'))->render();

        return response()->json([
            'products'   => $productsHtml,
            'pagination' => $paginationHtml,
        ]);
    }
}
