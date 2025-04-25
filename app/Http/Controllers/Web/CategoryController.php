<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\RequestModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


use function Ramsey\Uuid\v1;

class CategoryController extends Controller
{
    public function listCategory()
    {
        $categories = Category::withoutTrashed()->get();
        return view('admin.category.index', compact('categories'));
    }

    public function addCategory()
    {
        $categories = Category::withoutTrashed()->get();
        return view('admin.category.add', compact('categories'));
    }

    public function addPostCategory(CategoryRequest $request)
    {
        $data = $request->validated();

        if (auth()->user()->isAdmin()) {
            Category::create($data);
            return redirect()->route('admin.categories.listCategory')
                ->with('success', 'Thêm danh mục thành công.');
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'create',
                'model_type'  => 'category',
                'payload'     => $data,
                'status'      => 'pending',
            ]);
            return redirect()->route('admin.categories.listCategory')
                ->with('info', 'Yêu cầu thêm danh mục đã được gửi, chờ quản trị viên phê duyệt.');
        }

        return redirect()->route('admin.categories.listCategory')
            ->with('error', 'Bạn không có quyền thực hiện hành động này.');
    }

    public function editCategory($id)
    {
        $categories = Category::findOrFail($id);
        $category_parent = Category::withoutTrashed()->get();
        return view('admin.category.edit', compact('categories', 'category_parent'));
    }
    public function editPutCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        // Nếu id_parent = 0 (chọn "Không có"), chuyển thành null để không gây lỗi exists
        if ($request->id_parent === '0' || $request->id_parent === 0) {
            $request->merge(['id_parent' => null]);
        }

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'id_parent' => 'nullable|exists:categories,id',
            'status'    => ['required', Rule::in(['active','inactive'])],
        ], [
            'name.required' => 'Tên danh mục không được để trống',
            'name.max'      => 'Tên danh mục không được vượt quá 255 ký tự',
        ]);

        // Không cho chọn chính nó làm cha
        if ($validated['id_parent'] == $id) {
            return back()->withErrors(['id_parent' => 'Danh mục không thể là cha của chính nó.']);
        }

        // Kiểm tra quan hệ cha-con để tránh vòng lặp
        if (!empty($validated['id_parent'])) {
            $parent = Category::find($validated['id_parent']);
            if ($parent && $this->isDescendant($category, $parent)) {
                return back()->withErrors(['id_parent' => 'Danh mục không thể trở thành cha của danh mục con của chính nó.']);
            }
        }

        // Chặn chuyển trạng thái inactive nếu còn sản phẩm hoặc con active
        if ($validated['status'] === 'inactive') {
            if ($category->products()->count() > 0) {
                return back()->withErrors(['status' => 'Không thể ngừng hoạt động: danh mục vẫn còn sản phẩm.']);
            }
            if ($category->children()->where('status', 'active')->exists()) {
                return back()->withErrors(['status' => 'Không thể ngừng hoạt động: vẫn còn danh mục con đang hoạt động.']);
            }
        }

        if (auth()->user()->isAdmin()) {
            $category->update($validated);
            return redirect()->route('admin.categories.listCategory')
                ->with('success', 'Sửa danh mục thành công.');
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'update',
                'model_type'  => 'category',
                'model_id'    => $category->id,
                'payload'     => $validated,
                'status'      => 'pending',
            ]);
            return redirect()->route('admin.categories.listCategory')
                ->with('info', 'Yêu cầu chỉnh sửa danh mục đã được gửi, chờ quản trị viên phê duyệt.');
        }

        return redirect()->route('admin.categories.listCategory')
            ->with('error', 'Bạn không có quyền thực hiện hành động này.');
    }


    public function isDescendant($category, $potentialParent)
    {
        if (!$potentialParent) return false;
        if ($potentialParent->id == $category->id) return true;

        $children = Category::where('id_parent', $category->id)->get();
        foreach ($children as $child) {
            if ($this->isDescendant($child, $potentialParent)) {
                return true;
            }
        }
        return false;
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);

        // Kiểm tra điều kiện xóa tương tự trước
        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa danh mục vì vẫn còn sản phẩm.');
        }
        if ($category->children()->count() > 0) {
            foreach ($category->children as $child) {
                if ($child->products()->count() > 0) {
                    return redirect()->back()->with('error', 'Không thể xóa danh mục cha vì danh mục con vẫn còn sản phẩm.');
                }
            }
        }

        if (auth()->user()->isAdmin()) {
            // Xóa mềm danh mục và các con nếu có
            if ($category->children()->count() > 0) {
                foreach ($category->children as $child) {
                    $child->delete();
                }
            }
            $category->delete();
            return redirect()->route('admin.categories.listCategory')
                ->with('success', 'Xóa danh mục thành công.');
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'delete',
                'model_type'  => 'category',
                'model_id'    => $category->id,
                'payload'     => [
                    'name'      => $category->name,
                    'id_parent' => $category->id_parent,
                    'status'    => $category->status,
                ],
                'status'      => 'pending',
            ]);
            return redirect()->route('admin.categories.listCategory')
                ->with('info', 'Yêu cầu xóa danh mục đã được gửi, chờ quản trị viên phê duyệt.');
        }

        return redirect()->route('admin.categories.listCategory')
            ->with('error', 'Bạn không có quyền thực hiện hành động này.');
    }

    public function trash()
    {
        $categories = Category::onlyTrashed()->get();
        return view('admin.category.trash', compact('categories'));
    }

    public function restoreCategory($id)
    {
        $category = Category::withTrashed()->find($id);
        if (!$category) {
            return redirect()->route('admin.categories.trash')
                ->with('error', 'Danh mục không tồn tại.');
        }
        if (!$category->trashed()) {
            return redirect()->route('admin.categories.trash')
                ->with('error', 'Danh mục chưa bị xóa mềm.');
        }

        if (auth()->user()->isAdmin()) {
            $category->restore();
            return redirect()->route('admin.categories.listCategory')
                ->with('success', 'Khôi phục danh mục thành công.');
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'restore',
                'model_type'  => 'category',
                'model_id'    => $category->id,
                'payload'     => [
                    'name'      => $category->name,
                    'id_parent' => $category->id_parent,
                    'status'    => $category->status,
                ],
                'status'      => 'pending',
            ]);
            return redirect()->route('admin.categories.trash')
                ->with('info', 'Yêu cầu khôi phục danh mục đã được gửi, chờ quản trị viên phê duyệt.');
        }

        return redirect()->route('admin.categories.trash')
            ->with('error', 'Bạn không có quyền thực hiện hành động này.');
    }

    public function destroyPermanent($id)
    {
        $category = Category::withTrashed()->find($id);
        if (!$category) {
            return redirect()->route('admin.categories.trash')
                ->with('error', 'Danh mục không tồn tại.');
        }

        if (!auth()->user()->isAdmin()) {
            return redirect()->route('admin.categories.trash')
                ->with('error', 'Chỉ quản trị viên mới có quyền xóa vĩnh viễn.');
        }

        $category->forceDelete();
        return redirect()->route('admin.categories.trash')
            ->with('success', 'Danh mục đã bị xóa vĩnh viễn.');
    }
}
