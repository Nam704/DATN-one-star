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
        return view('admin.category.add');
    }


    public function addPostCategory(CategoryRequest $request)
{

    Category::create([
        'name' => $request->name,
        'status' => $request->status,
    ]);

    return redirect()->route('admin.categories.listCategory')->with('success', 'Thêm danh mục thành công');
}

public function editCategory($id){
    $categories = Category::findOrFail($id);
    return view('admin.category.edit')->with([
        'categories' => $categories
    ]);
}

public function editPutCategory(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ],
    [
        'name.required' => 'Tên danh mục không được trống',
        'name.max' => 'Tên danh mục không quá 255 ký tự',
    ]
    );

    $categories = Category::findOrFail($id);
    $categories->update([
        'name' => $request->name,
        'status' => $request->status,
    ]);

    return redirect()->route('admin.categories.listCategory')->with('success', 'Sửa danh mục thành công');
}

public function deleteCategory($id)
{
    $categories=Category::findOrFail($id);
        $categories->delete();
        return redirect()->route('admin.categories.listCategory')->with('success','Xóa thành công');
}

}
