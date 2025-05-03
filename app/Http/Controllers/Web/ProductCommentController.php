<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProductComment;
use Illuminate\Http\Request;

class ProductCommentController extends Controller
{
    // Hiển thị danh sách bình luận
    public function index()
    {
        $comments = ProductComment::with(['user', 'product'])->latest()->paginate(10);
        return view('admin.comment-product.index', compact('comments'));
    }

    // Xem và trả lời bình luận
    public function edit($id)
    {
        $comment = ProductComment::with(['product', 'parent', 'replies', 'user'])->find($id);

        $product = $comment->product;

        $comment = ProductComment::find($id);
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bình luận',
            ], 404);
        }

        return view('admin.comment-product.edit', compact('comment', 'product'));
    }

    public function update(Request $request, string $id)
    {

        return view('admin.blog.index', compact('blogs', 'categories'));
    }

    public function destroy($id)
    {
        try {
            $comment = ProductComment::findOrFail($id);

            $comment->replies()->delete();

            $comment->delete();

            return response()->json([
                'success' => true,
                'commentId' => $id
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa bình luận'
            ], 500);
        }
    }

    public function reply(Request $request, $parentId)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $parentComment = ProductComment::findOrFail($parentId);

        $reply = new ProductComment();
        $reply->product_id = $parentComment->product_id;
        $reply->user_id = auth()->id();
        $reply->parent_id = $parentId;
        $reply->comment = $request->comment;
        $reply->save();

        return back()->with('success', 'Đã trả lời bình luận');
    }
}
