<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        
        $comments = Comment::where('status', 'pending')
        ->with(['user', 'product']) // Lấy thông tin người dùng và sản phẩm liên quan
        ->latest() // Sắp xếp theo thời gian mới nhất
        ->paginate(20); // Phân trang 20 bình luận một trang

    return view('admin.comment.list', compact('comments'));
    }

    public function listapprove()
{
    // Lấy tất cả các bình luận đã duyệt
    $comments = Comment::where('status', 'active')->latest()->paginate(20);
    return view('admin.comment.listapprove', compact('comments'));
}

public function listreject()
{
    // Lấy tất cả các bình luận đã từ chối
    $comments = Comment::where('status', 'rejected')->latest()->paginate(20);
    return view('admin.comment.listrejected', compact('comments'));
}

    /**
     * Duyệt bình luận (chuyển status sang 'active').
     */
    public function approve($id)
    {
        $comment = Comment::findOrFail($id);  
        $comment->status = 'active';
        $comment->save();

        return back()->with('success', 'Đã duyệt bình luận.');
    }

    /**
     * Từ chối bình luận (chuyển status sang 'rejected').
     */
    public function reject($id)
    {
        $comment = Comment::findOrFail($id);

        $comment->status = 'rejected';
        $comment->save();

        return back()->with('success', 'Đã từ chối bình luận.');
    }

    public function show($id)
{
    // Lấy bình luận theo ID, kèm theo thông tin người dùng và sản phẩm
    $comment = Comment::with(['user', 'product'])->findOrFail($id);

    // Trả về view với thông tin bình luận
    return view('admin.comment.show', compact('comment'));
}

    /**
     * Xóa mềm bình luận.
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }

    public function listdelete()
{
    // Lấy tất cả bình luận đã bị xóa (soft delete)
    $comments = Comment::onlyTrashed()
        ->with(['user', 'product'])
        ->latest()
        ->paginate(20);

    return view('admin.comment.listdelete', compact('comments'));
}

public function restore($id)
{
    // Lấy bình luận đã xóa
    $comment = Comment::onlyTrashed()->findOrFail($id);
    
    // Khôi phục bình luận
    $comment->restore();
    
    // Kiểm tra trạng thái của bình luận để chuyển hướng tới trang phù hợp
    if ($comment->status === 'active') {
        return redirect()->route('admin.comments.listapprove')->with('success', 'Bình luận đã được khôi phục và chuyển đến danh sách duyệt.');
    } elseif ($comment->status === 'rejected') {
        return redirect()->route('admin.comments.listrejected')->with('success', 'Bình luận đã được khôi phục và chuyển đến danh sách từ chối.');
    }

    // Mặc định về trang danh sách bình luận
    return redirect()->route('admin.comments.index')->with('success', 'Bình luận đã được khôi phục.');
}


public function delete($id)
{
    // Xóa vĩnh viễn bình luận
    $comment = Comment::onlyTrashed()->findOrFail($id);
    $comment->forceDelete();

    return back()->with('success', 'Bình luận đã bị xóa vĩnh viễn.');
}

}
