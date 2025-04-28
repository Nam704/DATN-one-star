<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Comment;
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

        // Tự động tăng view mỗi lần xem chi tiết
        $product->increment('view');

        $relatedProducts = Product::where('id_category', $product->id_category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();
        $comments = Comment::with('user')->where('product_id', $id)->where('status', 'active')->get();
        return view('client.detail.index', compact('product', 'relatedProducts','comments'));
    }

    public function storecomment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'comment' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'comment' => $request->comment,
            'rating' => $request->rating ?? 5,
            'status' => 'pending', // Bình luận chờ duyệt
        ]);

        return back()->with('success', 'Bình luận của bạn đã được gửi thành công, vui lòng chờ duyệt!');
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
