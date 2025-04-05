<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Cart_details;
use App\Models\Category;
use App\Models\Slide;
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
        $categories = $this->categoryService->getCategories();
        // return ($categories);
        $homeSlides = Slide::whereJsonContains('display_locations', 'home')
            ->with(['primaryImage', 'secondaryImages'])
            ->get();

        $recommendedProducts = $this->getRecommendedProducts();
        return view('client.index', compact('categories','recommendedProducts', 'homeSlides'));
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
