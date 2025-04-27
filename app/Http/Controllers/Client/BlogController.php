<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Tag;
use App\Models\CategoryBlog;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        $categoryBlogs = CategoryBlog::all();
        $recent_blogs = Blog::where('status', '!=', 'draft') // Chỉ lấy bài viết có trạng thái không phải "draft"
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
    $blogs = Blog::where('status', '!=', 'draft') // Cũng áp dụng lọc tương tự cho trang phân trang
                ->latest()
                ->paginate(2);
        return view('client.blog.blogs', compact('tags', 'categoryBlogs', 'recent_blogs', 'blogs'));
    }

    public function show(string $id)
    {
        $tags = Tag::all();
        $categoryBlogs = CategoryBlog::all();
        $blog = Blog::with('tags', 'category')->find($id);

        if ($blog->status == 'draft' && !request()->has('view_id')) {
            return redirect()->route('client.home')->with('message_error', 'Bài viết này không thể xem vì nó đã bị ẩn.');
        }
        

        $recent_blogs = Blog::orderBy('created_at', 'desc')->where('status', '!=', 'draft')->limit(5)->get();
        $relatedblogs = Blog::where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->where('status', '!=', 'draft')
            ->limit(3)
            ->get();

        $shortText = Str::limit($blog->content ?? '', 50);

        return view('client.blog.detail-blog', compact('tags', 'categoryBlogs', 'blog', 'recent_blogs', 'relatedblogs', 'shortText'));
    }

}
