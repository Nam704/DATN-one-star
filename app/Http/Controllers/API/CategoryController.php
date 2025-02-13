<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // Lấy danh sách danh mục
    public function index()
    {
        // Lấy danh sách các category và trả về dưới dạng JSON
        $listCategory = Category::select('id', 'name', 'id_parent', 'status')->get();

        return response()->json([
            'message' => 'success',
            'data' => $listCategory
        ], 200);
    }


    public function getChildCategories($parentId)
    {
        // Lấy tất cả danh mục con có id_parent bằng $parentId
        $childCategories = Category::where('id_parent', $parentId)->get();

        return response()->json([
            'success' => true,
            'data' => $childCategories
        ]);
    }

    public function getRootCategories()
{
    
    $rootCategories = Category::whereNull(columns: 'id_parent')->get();

    return response()->json([
        'success' => true,
        'data' => $rootCategories
    ]);
}

    // Thêm mới danh mục
    public function addCategory(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:255|unique:categories,name',
                'status' => 'required|in:active,inactive', // Giới hạn status là active hoặc inactive
            ],
            [
                'name.required' => 'Tên danh mục không được để trống',
                'name.max' => 'Tên danh mục không được vượt quá 255 ký tự',
                'name.unique' => 'Tên danh mục đã tồn tại',
                'status.required' => 'Trạng thái không được để trống',
                'status.in' => 'Trạng thái phải là active hoặc inactive',
            ]
        );

        // Nếu lỗi, trả về thông báo lỗi
        if ($validate->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        // Lưu danh mục mới
        $data = [
            'name' => $request->name,
            'id_parent' => $request->id_parent, // Có thể null nếu không có danh mục cha
            'status' => $request->status,
        ];

        $category = Category::create($data);

        // Trả về kết quả thành công
        return response()->json([
            'message' => 'Category created successfully',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'id_parent' => $category->id_parent,
                'status' => $category->status,
            ]
        ], 201);
    }
}
