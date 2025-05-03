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
        $request->validate([
            'title' => 'required|string|max:255|unique:blogs,title',
            'content' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'title.required' => 'Tiêu đề bài viết là bắt buộc.',
            'title.string' => 'Tiêu đề bài viết phải là một chuỗi ký tự.',
            'title.max' => 'Tiêu đề bài viết không được quá 255 ký tự.',
            'title.unique' => 'Tiêu đề bài viết đã tồn tại. Vui lòng chọn tiêu đề khác.',

            'content.required' => 'Nội dung bài viết là bắt buộc.',
            'content.string' => 'Nội dung bài viết phải là một chuỗi ký tự.',

            'thumbnail.required' => 'Không được để trống ảnh',
            'thumbnail.image' => 'Ảnh bài viết phải là một tệp hình ảnh.',
            'thumbnail.mimes' => 'Ảnh bài viết phải có định dạng jpeg, png, jpg, gif, webp hoặc svg.',
            'thumbnail.max' => 'Ảnh bài viết không được vượt quá 2MB.',
        ]);

        $blog_data = $this->BlogService->createBlog($request);

        $blogs = Blog::latest()->get();

        return view('admin.blog.index', compact('blogs'));
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

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:blogs,title,' . $id,
            'content' => 'required|string',
            'category_id' => 'required|exists:category_blog,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', 
        ],
    [
        'title.required' => 'Vui lòng nhập tên bài viết.',
        'title.max' => 'Nhập tối đa 255 từ.',
        'title.unique' => 'Tiêu đề bị trùng',
        'content.required' => 'Vui lòng nhập nội dung bài viết.',
        'category_id.required' => 'Vui lòng chọn danh mục.',
        'thumbnail.image' => 'Vui lòng chọn một file ảnh hợp lệ.',
        'thumbnail.mimes' => 'File ảnh phải có định dạng jpeg, png, jpg, gif, webp hoặc svg.',
        'thumbnail.max' => 'File ảnh không được vượt quá 2MB.'
    ]);

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
            return response()->json(['success' => true, 'message' => 'Bài viết đã được xóa!']);
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
