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
            $productsQuery->whereIn('id_category', $request->input('categories'));
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
        $products = $productsQuery->with(['variants.importDetails'])->paginate(12);

        // Return the view with data
        return view('client.shops.shop', compact('categories', 'brands', 'products', 'maxPrice'));
    }

    public function filter(Request $request)
    {
        // Lấy các tham số lọc từ request
        $categories = $request->input('categories', []);
        $brands = $request->input('brands', []);
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', 50000000);

        // Xử lý giá tối đa mặc định (nếu không có input, giá mặc định là 50.000.000)
        $maxPriceDefault = 50000000;
        $maxPrice = (int)str_replace('.', '', $request->input('max_price', $maxPriceDefault));

        // Khởi tạo truy vấn sản phẩm
        $productsQuery = Product::where('status', 'active');

        // Lọc theo danh mục (input là mảng)
        if ($request->has('categories')) {
            $categories = $request->input('categories');
            if (is_string($categories)) {
                $categories = explode(',', $categories);
            }
            $productsQuery->whereIn('id_category', $categories);
        }

        // Lọc theo thương hiệu (input là mảng)
        if ($request->has('brands')) {
            $brands = $request->input('brands');
            if (is_string($brands)) {
                $brands = explode(',', $brands);
            }
            $productsQuery->whereIn('id_brand', $brands);
        }

        // Lọc theo khoảng giá
        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = (float)$request->input('min_price', 0);
            $maxPriceInput = (float)$request->input('max_price', $maxPrice);
            $productsQuery->whereHas('variants.importDetails', function ($query) use ($minPrice, $maxPriceInput) {
                $query->whereBetween('expected_price', [$minPrice, $maxPriceInput]);
            });
        }

        // Lọc theo tên sản phẩm nếu có tham số 'search'
        if (!empty($search)) {
            $productsQuery->where('name', 'like', '%' . $search . '%');
        }

        // Lấy sản phẩm với phân trang (12 sản phẩm/trang)
        $products = $productsQuery->with(['variants.importDetails'])->paginate(12);

        // Render các view con thành HTML
        $productsHtml = view('client.shops.product-list', compact('products'))->render();
        $paginationHtml = view('client.shops.pagination', compact('products'))->render();

        return response()->json([
            'products'   => $productsHtml,
            'pagination' => $paginationHtml,
        ]);
    }

}
