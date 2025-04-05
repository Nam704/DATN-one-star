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
            'author' => 'required|string',
        ]);

        // Lưu bình luận vào cơ sở dữ liệu
        $comment = new ProductComment();
        $comment->product_id = $productId;
        $comment->user_id = auth()->id(); // Nếu người dùng đã đăng nhập, bạn có thể lấy user_id
        $comment->comment = $request->comment;
        $comment->save();

        // Trả về dữ liệu bình luận vừa thêm (bao gồm tên người dùng, thời gian, nội dung, v.v.)
        return response()->json([
            'comment' => $comment,
            'user_name' => auth()->user()->name, // Hoặc sử dụng tên của tác giả nếu cần
            'created_at' => $comment->created_at->format('F d, Y'),
        ]);
    }
}
