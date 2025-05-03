<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Tag;
use App\Models\CategoryBlog;
use App\Services\BlogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    protected $BlogService;

    public function __construct(BlogService $BlogService)
    {
        $this->BlogService = $BlogService;
    }
    public function index()
    {
        $blogs = Blog::with('category', 'tags')->whereNull('deleted_at')->get();
        $categories = CategoryBlog::with(['blogs' => function ($query) {
            $query->whereNull('deleted_at')->with('tags');
        }])->get();

        return view('admin.blog.index', compact('categories', 'blogs'),);
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
        $categories = CategoryBlog::with(['blogs' => function ($query) {
            $query->whereNull('deleted_at')->with('tags');
        }])->get();
        return view('admin.blog.index', compact('blogs', 'categories'));
    }

    public function show(string $id)
    {
        $blog = Blog::find($id);
        $tags = $blog->tags;
        return view('admin.blog.detail', compact('blog', 'tags'));
    }

    public function edit(string $id)
    {
        $tags = Tag::all();
        $categoryBlog = CategoryBlog::all();
        $blog = Blog::find($id);
        return view('admin.blog.edit', compact('tags', 'categoryBlog', 'blog'));
    }

    public function update(Request $request, string $id)
    {
        $blog = $this->BlogService->updateBlog($request, $id);
        $blogs = Blog::latest()->get();
        $categories = CategoryBlog::with(['blogs' => function ($query) {
            $query->whereNull('deleted_at')->with('tags');
        }])->get();
        return view('admin.blog.index', compact('blogs', 'categories'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $blog = Blog::findOrFail($id);
            $blog->delete(); // Xóa mềm

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Bài viết đã được xóa mềm!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi khi xóa bài viết!', 'error' => $e->getMessage()]);
        }
    }

    public function trash()
    {
        $trashedBlogs = Blog::with('category')->onlyTrashed()->get();
        return view('admin.blog.trash', compact('trashedBlogs'));
    }

    public function restore($id)
    {
        try {
            $blog = Blog::onlyTrashed()->findOrFail($id);
            $blog->restore();

            return response()->json(['success' => true, 'message' => 'Bài viết đã được khôi phục!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi khi khôi phục bài viết!']);
        }
    }
}
