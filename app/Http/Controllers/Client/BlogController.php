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
        $recent_blogs = Blog::orderBy('created_at', 'desc')->limit(5)->get();
        $blogs = Blog::latest()->get();
        return view('client.blog.blogs', compact('tags', 'categoryBlogs', 'recent_blogs', 'blogs'));
    }

    public function show(string $id)
    {
        $tags = Tag::all();
        $categoryBlogs = CategoryBlog::all();
        $blog = Blog::with('tags', 'category')->find($id);

        if (!$blog) {
            return abort(404, 'Bài viết không tồn tại.');
        }

        $recent_blogs = Blog::orderBy('created_at', 'desc')->limit(5)->get();
        $relatedblogs = Blog::where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->limit(3)
            ->get();

        $shortText = Str::limit($blog->content ?? '', 50);

        return view('client.blog.detail-blog', compact('tags', 'categoryBlogs', 'blog', 'recent_blogs', 'relatedblogs', 'shortText'));
    }

}
