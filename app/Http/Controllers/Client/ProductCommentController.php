<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductComment;

class ProductCommentController extends Controller
{
    public function addComment(Request $request, $productId)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = new ProductComment();
        $comment->product_id = $productId;
        $comment->user_id = auth()->id(); // Lấy ID người dùng đăng nhập
        $comment->comment = $request->comment;
        $comment->save();

        return response()->json([
            'comment' => $comment,
            'user_name' => auth()->user()->name,
            'created_at' => $comment->created_at->format('F d, Y'),
        ]);
    }
}
