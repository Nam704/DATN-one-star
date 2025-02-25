<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    protected $category;
    public function __construct(Category $category)
    {
        $this->category = $category;
    }
    public function getCategories()
    {
        // Lấy tất cả các categories và danh mục con
        return $this->category->with('children', 'products')->get();
    }
    public function getCategoryById($id)
    {
        return Category::find($id);
    }
    public function createCategory($data)
    {
        try {
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'id_parent' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if ($value !== 0 && $value !== '0') {
                            // Kiểm tra giá trị phải tồn tại trong bảng categories nếu khác 0
                            if (!Category::where('id', $value)->exists()) {
                                $fail('Danh mục cha không tồn tại.');
                            }
                        }
                    },
                    function ($attribute, $value, $fail) {
                        // Ngăn chặn việc tạo danh mục cấp 3
                        if ($value) {
                            $parentCategory = Category::find($value);
                            if ($parentCategory && $parentCategory->id_parent) {
                                $fail('Không thể tạo danh mục cấp 3. Chỉ hỗ trợ tối đa 2 cấp.');
                            }
                        }
                    }
                ],
            ]);

            if ($validator->fails()) {
                Log::error("message: " . $validator->errors());
            }
            $data = $validator->validated();
            $category = Category::create($data);
            return $category;
        } catch (ValidationException $e) {
            throw $e;
        }
    }
    public function updateCategory($id, $data)
    {
        $category = Category::find($id);
        if (!$category) {
            return null;
        }
        $category->update($data);
        return $category;
    }
    public function deleteCategory($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return true;
        }
        return false;
    }
}
