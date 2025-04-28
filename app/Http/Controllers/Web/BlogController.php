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
        // Validate the input fields
        $request->validate([
            'title' => 'required|string|max:5000|unique:blogs,title',
            'content' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'title.required' => 'Tiêu đề bài viết là bắt buộc.',
            'title.string' => 'Tiêu đề bài viết phải là một chuỗi ký tự.',
            'title.max' => 'Tiêu đề bài viết không được quá 5000 ký tự.',
            'title.unique' => 'Tiêu đề bài viết đã tồn tại. Vui lòng chọn tiêu đề khác.',
        
            'content.required' => 'Nội dung bài viết là bắt buộc.',
            'content.string' => 'Nội dung bài viết phải là một chuỗi ký tự.',
    
            'thumbnail.required' => 'Không được để trống ảnh',
            'thumbnail.image' => 'Ảnh bài viết phải là một tệp hình ảnh.',
            'thumbnail.mimes' => 'Ảnh bài viết phải có định dạng jpeg, png, jpg, gif, webp hoặc svg.',
            'thumbnail.max' => 'Ảnh bài viết không được vượt quá 2MB.',
        ]);
    
        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');

            if ($file->isValid()) {
                $imagePath = $file->store('blogs', 'public');
            } else {
                return back()->withErrors(['thumbnail' => 'Ảnh không hợp lệ hoặc bị lỗi khi tải lên.'])->withInput();
            }
        }
    
        // Create the blog using the service
        $blog_data = $this->BlogService->createBlog($request, $imagePath);
    
        // Get the latest blogs
        $blogs = Blog::latest()->get();
    
        // Redirect with success message
        return redirect()->route('admin.blogs.index')->with('success', 'Bài viết đã được tạo thành công!');
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
            'title' => 'required|string|max:300|unique:blogs,title,' . $id, // Tên bài viết là bắt buộc, chuỗi và tối đa 255 ký tự
            'content' => 'required|string', // Nội dung bài viết là bắt buộc và phải là chuỗi
            'category_id' => 'required|exists:category_blog,id', // Danh mục phải được chọn và phải tồn tại trong bảng categories
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Ảnh là tùy chọn, nhưng nếu có, phải là hình ảnh và có kích thước tối đa là 2MB
        ],
    [
        'title.required' => 'Vui lòng nhập tên bài viết.',
        'title.max' => 'Nhập tối đa 300 từ.',
        'title.unique' => 'Tiêu đề bị trùng',
        'content.required' => 'Vui lòng nhập nội dung bài viết.',
        'category_id.required' => 'Vui lòng chọn danh mục.',
        'thumbnail.image' => 'Vui lòng chọn một file ảnh hợp lệ.',
        'thumbnail.mimes' => 'File ảnh phải có định dạng jpeg, png, jpg, gif, webp hoặc svg.',
        'thumbnail.max' => 'File ảnh không được vượt quá 2MB.'
    ]);

        $blog = $this->BlogService->updateBlog($request, $id);
        $blogs = Blog::latest()->get();
        return view('admin.blog.index', compact('blogs'));
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

        // Sau khi restore, nếu trạng thái hiện tại là 'draft' thì đổi trạng thái
        if ($blog->status === 'draft') {
            $blog->status = 'published'; // hoặc 'published' tùy bạn muốn
            $blog->save();
        }

        return response()->json(['success' => true, 'message' => 'Bài viết đã được khôi phục!']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Lỗi khi khôi phục bài viết!']);
    }
}
}
