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
    $search = $request->input('search', ''); // Nếu cần lọc theo tên sản phẩm

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

    // Lọc theo khoảng giá
    if ($minPrice != 0 || $maxPrice != 50000000) {
        $productsQuery->whereHas('variants.importDetails', function ($query) use ($minPrice, $maxPrice) {
            $query->whereBetween('expected_price', [$minPrice, $maxPrice]);
        });
    }

    // Lọc theo tên sản phẩm nếu có tham số 'search'
    if (!empty($search)) {
        $productsQuery->where('name', 'like', '%' . $search . '%');
    }

    // Sắp xếp sản phẩm theo yêu cầu của người dùng
    switch ($orderBy) {
        case 'price_asc': // Giá từ thấp đến cao
            $productsQuery->orderByRaw('(SELECT expected_price FROM import_details WHERE import_details.id_variant = products.id LIMIT 1) ASC');
            break;
        case 'price_desc': // Giá từ cao đến thấp
            $productsQuery->orderByRaw('(SELECT expected_price FROM import_details WHERE import_details.id_variant = products.id LIMIT 1) DESC');
            break;
        case 'name_asc': // Tên A → Z
            $productsQuery->orderBy('name', 'asc');
            break;
        case 'name_desc': // Tên Z → A
            $productsQuery->orderBy('name', 'desc');
            break;
        default: // Mặc định sắp xếp theo ngày tạo mới nhất
            $productsQuery->orderBy('created_at', 'desc');
            break;
    }

    // Lấy danh sách sản phẩm kèm phân trang
    $products = $productsQuery->with(['variants.importDetails'])->paginate(9);

    // Render view thành HTML
    $productsHtml = view('client.shops.product-list', compact('products'))->render();
    $paginationHtml = view('client.shops.pagination', compact('products'))->render();

    return response()->json([
        'products'   => $productsHtml,
        'pagination' => $paginationHtml,
    ]);
}



}
