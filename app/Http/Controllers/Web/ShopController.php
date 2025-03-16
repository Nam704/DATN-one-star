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
        $orderBy = $request->input('orderby', 'default');
        $categories = $request->input('categories', []);
        $brands = $request->input('brands', []);
        $minPrice = (float) $request->input('min_price', 0);
        $maxPrice = (float) $request->input('max_price', 50000000);
        $search = $request->input('search', '');

        $productsQuery = Product::where('status', 'active');

        if (!empty($categories)) {
            if (is_string($categories)) {
                $categories = explode(',', $categories);
            }
            $productsQuery->whereIn('id_category', $categories);
        }
        if (!empty($brands)) {
            if (is_string($brands)) {
                $brands = explode(',', $brands);
            }
            $productsQuery->whereIn('id_brand', $brands);
        }
        if ($minPrice != 0 || $maxPrice != 50000000) {
            $productsQuery->whereHas('variants.importDetails', function ($query) use ($minPrice, $maxPrice) {
                $query->whereBetween('expected_price', [$minPrice, $maxPrice]);
            });
        }
        if (!empty($search)) {
            $productsQuery->where('name', 'like', '%' . $search . '%');
        }

        switch ($orderBy) {
            case 'price_asc':
                $productsQuery->orderByRaw(
                    '(SELECT expected_price FROM import_details
                      WHERE import_details.id_product_variant IN
                      (SELECT id FROM product_variants WHERE product_variants.id_product = products.id)
                      ORDER BY expected_price ASC LIMIT 1) ASC'
                );
                break;
            case 'price_desc':
                $productsQuery->orderByRaw(
                    '(SELECT expected_price FROM import_details
                      WHERE import_details.id_product_variant IN
                      (SELECT id FROM product_variants WHERE product_variants.id_product = products.id)
                      ORDER BY expected_price DESC LIMIT 1) DESC'
                );
                break;
            case 'name_asc':
                $productsQuery->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $productsQuery->orderBy('name', 'desc');
                break;
            default:
                $productsQuery->orderBy('created_at', 'desc');
                break;
        }

        $products = $productsQuery->with(['variants.importDetails'])->paginate(9);

        $productsHtml = view('client.shops.product-list', compact('products'))->render();
        $paginationHtml = view('client.shops.pagination', compact('products'))->render();

        return response()->json([
            'products'   => $productsHtml,
            'pagination' => $paginationHtml,
        ]);
    }


}
