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
        
        // Check nếu muốn chuyển sang 'inactive' nhưng còn sản phẩm
    if ($request->status === 'inactive' && $category->products()->count() > 0) {
        return back()->withErrors(['status' => 'Không thể ngừng hoạt động danh mục còn chứa sản phẩm.']);
    } 

       // 5. Không cho chuyển sang 'inactive' nếu có danh mục con đang active
       if (
        $request->status === 'inactive' &&
        $category->children()->where('status', 'active')->exists()
    ) {
        return back()->withErrors(['status' => 'Không thể ngừng hoạt động khi vẫn còn danh mục con đang hoạt động.']);
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
        
        // Kiểm tra xem danh mục này có sản phẩm không
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.listCategory')->with('error', 'Không thể xóa danh mục vì vẫn còn sản phẩm.');
        }
    
        // Kiểm tra xem danh mục này có danh mục con không
        if ($category->children()->count() > 0) {
            // Kiểm tra nếu danh mục con có sản phẩm
            foreach ($category->children as $child) {
                if ($child->products()->count() > 0) {
                    return redirect()->route('admin.categories.listCategory')->with('error', 'Không thể xóa danh mục cha vì danh mục con vẫn còn sản phẩm.');
                }
            }
    
            // Nếu không có sản phẩm nào trong danh mục con, xóa danh mục con trước
            foreach ($category->children as $child) {
                $child->delete();
            }
        }
    
        // Xóa danh mục 
        $category->delete();
        return redirect()->route('admin.categories.listCategory')->with('success', 'Xóa danh mục thành công.');
    }
    public function trash()
{
    $categories = Category::onlyTrashed()->get();
    return view('admin.category.trash', compact('categories'));
}

public function restoreCategory($id)
{
    $category = Category::onlyTrashed()->findOrFail($id);
    $category->restore();

    return redirect()->route('admin.categories.listCategory')->with('success', 'Danh mục đã được khôi phục!');
}
 // Phương thức xóa vĩnh viễn danh mục
 public function destroyPermanent($id)
 {
     // Tìm danh mục đã bị xóa mềm
     $category = Category::onlyTrashed()->findOrFail($id);

     // Xóa vĩnh viễn
     $category->forceDelete();

     // Trở lại trang danh sách danh mục
     return redirect()->route('admin.categories.trash')->with('success', 'Danh mục đã bị xóa vĩnh viễn!');
 }
    
}
