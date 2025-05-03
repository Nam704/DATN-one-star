<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;
    function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function detail($id)
    {
        $product = $this->productService->productDetail($id);
        $relatedProducts = Product::where('id_category', $product->id_category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        $comments = $product->comments()
            ->whereNull('parent_id')
            ->with('user')
            ->get();

        $product_comment = Product::withCount('comments')->find($id);
        $totalComments = $product_comment->comments_count;

        return view('client.detail.index', compact('product', 'relatedProducts', 'comments', 'totalComments'));
    }

    public function related($id)
    {
        // Lấy thông tin sản phẩm chính
        $product = Product::findOrFail($id);

        // Lấy các sản phẩm liên quan theo cột id_category
        $relatedProducts = Product::where('id_category', $product->id_category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        foreach ($relatedProducts as $related) {
            $prices = $related->getPriceRange(); // Phương thức này phải được định nghĩa trong model Product
            $related->min_price = $prices->min_price;
            $related->max_price = $prices->max_price;
        }
        return view('client.detail.product-info', compact('product', 'relatedProducts'));
    }
}
