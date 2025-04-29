<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        
        $comments = Comment::where('status', 'active')
        ->with(['user', 'product']) 
        ->latest() 
        ->paginate(20); 

    return view('admin.comment.list', compact('comments'));
    }

    public function show($id)
{
    $comment = Comment::with(['user', 'product'])->findOrFail($id);
    return view('admin.comment.show', compact('comment'));
}

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->status = 'rejected';
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

    return redirect()->route('admin.comments.index')->with('success', 'Bình luận đã được hiện lại.');
}


public function delete($id)
{
    // Xóa vĩnh viễn bình luận
    $comment = Comment::onlyTrashed()->findOrFail($id);
    $comment->forceDelete();

    return back()->with('success', 'Bình luận đã bị xóa vĩnh viễn.');
}

}
