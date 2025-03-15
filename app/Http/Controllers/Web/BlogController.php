<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Tag;
use App\Models\CategoryBlog;
use App\Services\BlogService;

class BlogController extends Controller
{
    protected $BlogService;

    public function __construct(BlogService $BlogService)
    {
        $this->BlogService = $BlogService;
    }
    public function index()
    {
        $blogs = Blog::with('category', 'tags')->get();
        return view('admin.blog.index', compact('blogs'));
    }

    public function create()
    {
        $tags = Tag::all();
        $categoryBlog = CategoryBlog::all();
        return view('admin.blog.create', compact('tags', 'categoryBlog'));
    }

    public function store(Request $request)
    {
        $blog_data = $this->BlogService->createBlog($request);
        // Lấy danh sách blog mới nhất
        $blogs = Blog::latest()->get();

        return view('admin.blog.index', compact('blogs'));
    }

    public function show(string $id)
    {
        $blog = Blog::find($id);
        return view('admin.blog.detail', compact('blog'));
    }

    public function edit(string $id)
    {
        $tags = Tag::all();
        $categoryBlog = CategoryBlog::all();
        $blog = Blog::find($id);
        return view('admin.blog.create', compact('tags', 'categoryBlog'));
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
