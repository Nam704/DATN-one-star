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
        return view('admin.comment_product.index', compact('comments'));
    }
}
