<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RequestModel;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Tag;
use App\Models\CategoryBlog;
use App\Services\BlogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        // 1. Validate & format data (dùng service helper)
        $formatted = $this->BlogService->processBlogData($request);

        // 2. Nếu là admin → tạo ngay
        if (auth()->user()->isAdmin()) {
            $blog = $this->BlogService->createBlog($request);
            return redirect()->route('admin.blog.index')
                ->with('success', 'Bài viết đã được tạo thành công.');
        }

        // 3. Nếu là employee → tạo pending request
        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'create',
                'model_type'  => 'blog',
                'model_id'    => null,
                'payload'     => $formatted,       // sẽ tự chuyển thành JSON
                'status'      => 'pending',
            ]);

            return redirect()->route('admin.blogs.index')
                ->with('info', 'Yêu cầu tạo bài viết đã được gửi, chờ admin phê duyệt.');
        }

        abort(403, 'Bạn không có quyền thực hiện hành động này.');
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
        $blog = Blog::findOrFail($id);
        $formatted = $this->BlogService->processBlogData($request);

        if (auth()->user()->isAdmin()) {
            $this->BlogService->updateBlog($request, $id);
            return redirect()->route('admin.blog.index')
                ->with('success', 'Bài viết đã được cập nhật thành công.');
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'update',
                'model_type'  => 'blog',
                'model_id'    => $blog->id,
                'payload'     => $formatted,
                'status'      => 'pending',
            ]);

            return redirect()->route('admin.blogs.index')
                ->with('info', 'Yêu cầu cập nhật bài viết đã được gửi, chờ admin phê duyệt.');
        }

        abort(403);
    }


    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if (auth()->user()->isAdmin()) {
            $blog->delete();
            return response()->json([
                'success' => true,
                'message' => 'Bài viết đã được xóa mềm thành công.'
            ]);
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'delete',
                'model_type'  => 'blog',
                'model_id'    => $blog->id,
                'payload'     => [
                    'title'       => $blog->title,
                    'content'     => Str::limit($blog->content, 100),
                    'category_id' => $blog->category_id,
                    'tags'        => $blog->tags->pluck('id')->toArray(),
                    'thumbnail'   => $blog->thumbnail,
                    'status'      => $blog->status,
                ],
                'status'      => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu xóa bài viết đã được gửi, chờ admin phê duyệt.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bạn không có quyền thực hiện hành động này.'
        ], 403);
    }


    public function trash()
    {
        $trashedBlogs = Blog::with('category')->onlyTrashed()->get();
        return view('admin.blog.trash', compact('trashedBlogs'));
    }

    public function restore($id)
    {
        $blog = Blog::withTrashed()->findOrFail($id);

        if (auth()->user()->isAdmin()) {
            $blog->restore();
            return response()->json([
                'success' => true,
                'message' => 'Bài viết đã được khôi phục thành công.'
            ]);
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'restore',
                'model_type'  => 'blog',
                'model_id'    => $blog->id,
                'payload'     => [
                    'title'     => $blog->title,
                    'status'    => $blog->status,
                ],
                'status'      => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu khôi phục bài viết đã được gửi, chờ admin phê duyệt.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bạn không có quyền thực hiện hành động này.'
        ], 403);
    }
}
