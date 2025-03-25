<?php

namespace App\Services;

use App\Models\CategoryBlog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class CategoryBlogService
{
    protected $categoryBlog;

    public function __construct(CategoryBlog $categoryBlog)
    {
        $this->categoryBlog = $categoryBlog;
    }

    public function getCategories()
    {
        // Lấy tất cả danh mục
        return $this->categoryBlog->all();
    }

    public function getCategoryBlogById($id)
    {
        // Lấy danh mục theo id
        return CategoryBlog::find($id);
    }

    public function createCategoryBlog($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:category_blog,slug',
        ]);

        if ($validator->fails()) {
            Log::error("Validation error:", $validator->errors()->toArray());
            return ['status' => 'error', 'errors' => $validator->errors()];
        }

        $slug = isset($data['slug']) ? $data['slug'] : Str::slug($data['name']);

        $category = CategoryBlog::create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);
        return ['status' => 'success', 'data' => $category];
    }

    public function updateCategoryBlog($id, $data)
    {
        $categoryBlog = CategoryBlog::find($id);
        if (!$categoryBlog) {
            return null;
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error("Validation error:", $validator->errors()->toArray());
            return null;
        }

        $categoryBlog->update($validator->validated());
        return $categoryBlog;
    }

    public function deleteCategoryBlog($id)
    {
        $categoryBlog = CategoryBlog::find($id);
        if ($categoryBlog) {
            $categoryBlog->delete();
            return true;
        }
        return false;
    }
}
