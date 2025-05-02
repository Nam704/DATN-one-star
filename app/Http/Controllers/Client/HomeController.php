<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Cart_details;
use App\Models\Category;
use App\Models\Product;
use App\Models\Product_variant;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $categoryService;
    protected $productService;

    function __construct(CategoryService $categoryService, ProductService $productService)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
    }
    function index()
    {
        $category = $this->categoryService->getCategory();
        $categories = $this->categoryService->getCategories();

        $recommendedProducts = $this->getRecommendedProducts();

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
        $brands = Brand::where('status', 'active')
            ->withCount('products') // Đếm số sản phẩm liên quan
            ->get();
        return view('client.index', compact('categories', 'recommendedProducts', 'banners', 'brands', 'category'));
    }

    private function getRecommendedProducts($limit = 10)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $userCart = Cart::where('id_user', $userId)->first();

            if ($userCart) {
                $cartVariants = Cart_details::where('id_cart', $userCart->id)
                    ->pluck('id_variant')
                    ->toArray();

                if (!empty($cartVariants)) {
                    $productsFromVariants = Product_variant::whereIn('id', $cartVariants)
                        ->pluck('id_product')
                        ->toArray();
                    $cartProducts = Product::whereIn('id', $productsFromVariants)->get();

                    if ($cartProducts->count() > 0) {
                        $categoryIds = [];

                        foreach ($cartProducts as $product) {
                            $categoryIds[] = $product->id_category;
                        }

                        $recommendedQuery = Product::whereIn('id_category', $categoryIds)
                            ->whereNotIn('id', $productsFromVariants);

                        $recommendedProducts = $recommendedQuery->limit($limit)->get();
                        if ($recommendedProducts->count() < $limit) {
                            $existingIds = $recommendedProducts->pluck('id')->toArray();
                            $additionalProducts = Product::whereNotIn('id', array_merge($existingIds, $productsFromVariants))
                                ->latest()
                                ->limit($limit - $recommendedProducts->count())
                                ->get();

                            return $recommendedProducts->concat($additionalProducts);
                        }

                        return $recommendedProducts;
                    }
                }
            }
        }
        return Product::latest()->limit($limit)->get();
    }
}
