<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class CategoryController extends Controller
{
    public function listCategory(){
        $categories = Category::all();
        return view('admin.category.index')->with([
            'categories' => $categories
        ]);
    }

    public function addCategory(){
        $categories=Category::all();
        return view('admin.category.add')->with([
            'categories' => $categories
        ]);
    }


    public function addPostCategory(CategoryRequest $request)
{
  $category= Category::create([
        'name' => $request->name,
        'id_parent'=>$request->id_parent,
        'status' => $request->status ?? 1,
    ]);
    return redirect()->route('admin.categories.listCategory')->with('success', 'Thêm danh mục thành công');
    return response()->json([
        'success' => true,
        'category' => [
            'id' => $category->id,
            'name' => $category->name,
            'status' => $category->status,
        ],
    ]);
}

public function addCategoryProduct(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'id_parent' => 'nullable|exists:categories,id',
    ]);

    try {
        $category = new Category();
        $category->name = $request->name;
        $category->id_parent = $request->id_parent;
        $category->save();

        return response()->json(['success' => true, 'message' => 'Danh mục đã được thêm thành công.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
}


public function editCategory($id){
    $categories = Category::findOrFail($id);

    $category_parent=Category::all();
    return view('admin.category.edit')->with([
        'categories' => $categories,
        'category_parent' => $category_parent
    ]);
}

public function editPutCategory(Request $request, $id)
{
 
    $categories = Category::findOrFail($id); 
    $request->validate([
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
    
    $parentCategory = Category::find($request->id_parent);
    if ($parentCategory && $this->isDescendant($categories  , $parentCategory)) {
        return back()->withErrors(['id_parent' => 'Danh mục không thể trở thành cha của danh mục con của chính nó.']);
    }

    $categories->update([
        'name' => $request->name,
        'id_parent'=>$request->id_parent,
        'status' => $request->status,
    ]);

    return redirect()->route('admin.categories.listCategory')->with('success', 'Sửa danh mục thành công');
}
public function isDescendant($id,$parentCategory)
    {
        // Thêm logic kiểm tra ở đây
        return true; // Hoặc kết quả phù hợp
    }

public function deleteCategory($id)
{
    $categories=Category::findOrFail($id);
        $categories->delete();
        return redirect()->route('admin.categories.listCategory')->with('success','Xóa thành công');
}
}
