<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class CategoryController extends Controller
{
    public function listCategory()
    {
        $categories = Category::all();
        return view('admin.category.index')->with([
            'categories' => $categories
        ]);
    }

    public function addCategory()
    {
        $categories = Category::all();
        return view('admin.category.add')->with([
            'categories' => $categories
        ]);
    }


    public function addPostCategory(CategoryRequest $request)
    {
        Category::create([
            'name' => $request->name,
            'id_parent' => $request->id_parent,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.categories.listCategory')->with('success', 'Thêm danh mục thành công');
    }

    public function editCategory($id)
    {
        $categories = Category::findOrFail($id);
        $category_parent = Category::all();
        return view('admin.category.edit')->with([
            'categories' => $categories,
            'category_parent' => $category_parent
        ]);
    }

    public function editPutCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->validate(
            [
                'name' => 'required|string|max:255',
            ],
            [
                'name.required' => 'Tên danh mục không được trống',
                'name.max' => 'Tên danh mục không quá 255 ký tự',
            ]
        );

        if ($request->id_parent == $id) {
            return back()->withErrors(['id_parent' => 'Danh mục không thể là cha của chính nó.']);
        }

        if ($request->id_parent) {
            $parentCategory = Category::find($request->id_parent);
            if ($parentCategory && $this->isDescendant($category, $parentCategory)) {
                return back()->withErrors(['id_parent' => 'Danh mục không thể trở thành cha của danh mục con của chính nó.']);
            }
        }

        $category->update([
            'name' => $request->name,
            'id_parent' => $request->id_parent,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.categories.listCategory')->with('success', 'Sửa danh mục thành công');
    }


    public function isDescendant($category, $potentialParent)
    {
        if (!$potentialParent) {
            return false;
        }

        if ($potentialParent->id == $category->id) {
            return true;
        }
        $children = Category::where('id_parent', $category->id)->get();
        if ($children->isEmpty()) {
            return false;
        }
        foreach ($children as $child) {
            if ($child->id == $potentialParent->id || $this->isDescendant($child, $potentialParent)) {
                return true;
            }
        }

        return false;
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
    
        // Kiểm tra nếu danh mục có sản phẩm
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.listCategory')->with('error', 'Không thể xóa danh mục vì vẫn còn sản phẩm.');
        }
    
        $category->delete();
        return redirect()->route('admin.categories.listCategory')->with('success', 'Xóa thành công');
    }
}
