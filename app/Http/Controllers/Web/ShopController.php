<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
        $maxPrice = (int)str_replace('.', '', $request->input('max_price', 50000000));

        // Initialize the product query
        $productsQuery = Product::where('status', 'active');

        // Apply filters if present
        if ($request->has('categories')) {
            $selectedCategories = $request->input('categories');

            // Lấy danh sách các ID của danh mục được chọn và các danh mục con của nó
            $allCategoryIds = Category::whereIn('id', $selectedCategories)
                ->orWhereIn('id_parent', $selectedCategories)
                ->pluck('id')
                ->toArray();

            $productsQuery->whereIn('id_category', $allCategoryIds);
        }

        if ($request->has('brands')) {
            $productsQuery->whereIn('id_brand', $request->input('brands'));
        }
        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = (float)$request->input('min_price', 0);
            $maxPrice = (float)$request->input('max_price', $maxPrice);
            $productsQuery->whereHas('variants.importDetails', function ($query) use ($minPrice, $maxPrice) {
                $query->whereBetween('expected_price', [$minPrice, $maxPrice]);
            });
        }

        // Fetch paginated products
        $products = $productsQuery->with(['variants.importDetails'])->paginate(9);

        // Return the view with data
        return view('client.shops.shop', compact('categories', 'brands', 'products', 'maxPrice'));
    }

    public function filter(Request $request)
    {
        // Lấy các tham số lọc từ request
        $orderBy = $request->input('orderby', 'default');
        $categories = $request->input('categories', []);
        $brands = $request->input('brands', []);
        $minPrice = (float) $request->input('min_price', 0);
        $maxPrice = (float) $request->input('max_price', 50000000);
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
        if (!empty($brands)) {
            if (is_string($brands)) {
                $brands = explode(',', $brands);
            }
            $productsQuery->whereIn('id_brand', $brands);
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

        // Render view thành HTML
        $productsHtml = view('client.shops.product-list', compact('products'))->render();
        $paginationHtml = view('client.shops.pagination', compact('products'))->render();

        return response()->json([
            'products'   => $productsHtml,
            'pagination' => $paginationHtml,
        ]);
    }
}
