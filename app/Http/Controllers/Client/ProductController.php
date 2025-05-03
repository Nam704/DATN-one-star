<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Order_status;
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
    
        $user = auth()->user();
        $productId = $request->product_id;
    
        // Kiểm tra người dùng đã mua sản phẩm này chưa
        $hasPurchased = Order::where('id_user', $user->id)
            ->whereHas('orderDetails', function ($query) use ($productId) {
                $query->whereHas('productVariant', function ($q) use ($productId) {
                    $q->where('id_product', $productId); // Sửa: dùng id_product thay vì product_id
                });
            })
            ->whereHas('orderStatus', function ($query) {
                $query->where('name', 'Delivered');
            })
            ->exists();
    
        if (!$hasPurchased) {
            return response()->json(['message' => 'Bạn cần mua sản phẩm và nhận hàng trước khi bình luận.'], 400);
        }

        // kiểm tra sản phẩm đã bình luận rồi
        $existing = Comment::where('product_id', $request->product_id)
            ->where('user_id', auth()->id())
            ->first();
    
        if ($existing) {
            return response()->json(['message' => 'Cảm ơn bạn đã đánh giá và bình luận sản phẩm'], 400);
        }
    
        $comment = Comment::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'comment' => $request->comment,
            'rating' => $request->rating ?? 5,
            'status' => 'active',
        ]);
    
        return response()->json([
            'message' => 'Cảm ơn bạn đã đánh giá và bình luận sản phẩm của chúng tôi!',
            'comment' => $comment->comment,
            'rating' => $comment->rating
        ]);
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
