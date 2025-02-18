<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Cart;

class HomeController extends Controller 
{
    public function index()
    {
        // Lấy sản phẩm mới (mới nhất, status active)
        $newProducts = Product::with(['variants' => function($query) {
            $query->where('status', 'active')
                  ->orderBy('price', 'asc');
        }])
        ->where('status', 'active')
        ->latest()
        ->take(8)
        ->get();

        // Lấy sản phẩm giảm giá
        $saleProducts = Product::with(['variants' => function($query) {
            $query->onSale()  // Chỉ lấy variants đang giảm giá
                  ->orderBy('price_sale', 'asc');
        }])
        ->where('status', 'active')
        ->take(8)
        ->get();

        $categories = Category::with(['children', 'products' => function($query) {
            $query->where('status', 'active')->take(1);
        }])
        ->whereNull('id_parent')
        ->where('status', 'active')
        ->get();

        $products = Product::with(['variants' => function($query) {
            $query->where('status', 'active');
        }])
        ->where('status', 'active')
        ->take(4)
        ->get();

        $cart = Cart::with(['items.productVariant.product'])->first();
        if (!$cart) {
            $cart = new Cart();
        }
        return view('client.layouts.home', compact('newProducts', 'saleProducts', 'categories', 'products','cart'));
    }

    public function search(Request $request)
{
    $cart = Cart::with(['items.productVariant.product'])->first();
    if (!$cart) {
        $cart = new Cart();
    }

    $keyword = $request->keyword;
    $products = Product::where(function($query) use ($keyword) {
        $query->where('name', 'LIKE', "%{$keyword}%")
              ->orWhere('description', 'LIKE', "%{$keyword}%");
    })
    ->with(['variants' => function($query) {
        $query->where('status', 'active');
    }])
    ->where('status', 'active')
    ->paginate(12);

    return view('client.layouts.search', compact('products', 'keyword', 'cart'));
}
}
