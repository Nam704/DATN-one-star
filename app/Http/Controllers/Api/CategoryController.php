<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Category;


class CategoryController extends Controller
{
    protected $CategoryService;
    public function __construct(CategoryService $CategoryService)
    {
        $this->CategoryService = $CategoryService;
    }
    public function store(Request $request)
    {
        // Log::info($request->all());
        $data = $this->CategoryService->createCategory($request->all());
        // Log::info($data);
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function list(Request $request)
    {
        try {
            $categories = Category::all();
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
