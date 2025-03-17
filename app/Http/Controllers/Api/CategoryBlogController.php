<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CategoryBlogService;
use Illuminate\Support\Facades\Log;
use App\Models\CategoryBlog;

class CategoryBlogController extends Controller
{
    protected $CategoryBlogService;

    public function __construct(CategoryBlogService $CategoryBlogService){
        $this->CategoryBlogService = $CategoryBlogService;
    }

    public function store(Request $request){
        $data = $this->CategoryBlogService->createCategoryBlog($request->all());
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function list(Request $request)
    {
        try {
            $categories = CategoryBlog::all(); 
            return response()->json([
                'status' => 'success',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách danh mục!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
