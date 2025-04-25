<?php

namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Models\Brand;

use App\Models\RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index()


    {
        $brands = Brand::withoutTrashed()->latest()->get();
        return view('admin.brands.index', compact('brands'));
    }


    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(HttpRequest $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'name'   => [
                'required',
                'max:50',
                'unique:brands,name',
                'regex:/^[\p{L}\s]+$/u',
                'string'
            ],
            'status' => 'required|in:active,inactive'
        ], [
            'name.required' => 'The brand name must not be empty',
            'name.max'      => 'The brand name must not exceed 50 characters',
            'name.unique'   => 'This brand name already exists',
            'name.regex'    => 'Only letters are allowed in the name',
            'status.required' => 'Please select a status'
        ]);

        // If user is admin, create brand immediately
        if (auth()->user()->isAdmin()) {
            Brand::create($validated);
            return redirect()->route('admin.brands.index')
                ->with('success', 'Brand created successfully');
        }

        // If user is employee, store a pending request
        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'create',
                'model_type'  => 'brand',
                'payload'     => $validated,
                'status'      => 'pending',
            ]);
            return redirect()->route('admin.brands.index')
                ->with('info', 'Your request has been sent and is awaiting admin approval.');
        }

        // If neither admin nor employee, forbid
        return redirect()->route('admin.brands.index')
            ->with('error', 'Bạn không có quyền thực hiện hành động này.');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $user = auth()->user();

        Log::info("User ID {$user->id} (Role: {$user->role}) attempting to delete brand ID {$id}");

        if ($user->isEmployee()) {
            RequestModel::create([
                'employee_id' => $user->id,
                'action'      => 'delete',
                'model_type'  => 'brand',
                'model_id'    => $brand->id,
                'payload'     => [
                    'name' => $brand->name,
                    'status' => $brand->status,
                ],
                'status'      => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu xóa thương hiệu đã được gửi, chờ quản trị viên phê duyệt.'
            ]);
        }

        if ($user->isAdmin()) {
            $brand->delete();
            return response()->json([
                'success' => true,
                'message' => 'Thương hiệu đã được xóa thành công.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bạn không có quyền thực hiện hành động này.'
        ], 403);
    }
    public function trash()
    {
        $trashedBrands = Brand::onlyTrashed()->get();
        return view('admin.brands.trash', compact('trashedBrands'));
    }


    public function restore($id)
    {
        $brand = Brand::withTrashed()->find($id);

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Thương hiệu không tồn tại.'
            ], 404);
        }

        if (!$brand->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'Thương hiệu này chưa bị xóa mềm, không thể khôi phục.'
            ], 400);
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'restore',
                'model_type'  => 'brand',
                'model_id'    => $brand->id,
                'payload'     => [
                    'name' => $brand->name,
                    'status' => $brand->status,
                ],
                'status'      => 'pending',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu khôi phục đã được gửi, chờ quản trị viên phê duyệt.'
            ]);
        }

        if (auth()->user()->isAdmin()) {
            $brand->restore();
            return response()->json([
                'success' => true,
                'message' => 'Thương hiệu đã được khôi phục thành công.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bạn không có quyền thực hiện hành động này.'
        ], 403);
    }

    public function forceDelete($id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ quản trị viên mới có quyền xóa vĩnh viễn.'
            ], 403);
        }

        $brand = Brand::withTrashed()->find($id);
        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Thương hiệu không tồn tại.'
            ], 404);
        }

        $brand->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Thương hiệu đã được xóa vĩnh viễn.'
        ]);
    }

    public function toggleStatus($id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ quản trị viên mới có quyền thay đổi trạng thái.'
            ], 403);
        }

        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Thương hiệu không tồn tại.'
            ], 404);
        }

        $brand->status = $brand->status === 'active' ? 'inactive' : 'active';
        $brand->save();

        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đã được cập nhật thành công.',
            'newStatus' => $brand->status,
            'id' => $brand->id
        ]);
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        // Xác thực dữ liệu từ form
        $validated = $request->validate([
            'name' => [
                'required',
                'max:50',
                'regex:/^[\p{L}\s]+$/u',
                'string',
                Rule::unique('brands')->ignore($id)
            ],
            'status' => 'required|in:active,inactive'
        ], [
            'name.required' => 'Tên thương hiệu không được để trống',
            'name.max' => 'Tên thương hiệu không được vượt quá 50 ký tự',
            'name.regex' => 'Tên thương hiệu chỉ được chứa chữ cái và khoảng trắng',
            'name.unique' => 'Tên thương hiệu này đã tồn tại',
            'status.required' => 'Vui lòng chọn trạng thái'
        ]);

        // Nếu là admin, cập nhật ngay
        if (auth()->user()->isAdmin()) {
            $brand->update($validated);
            return redirect()->route('admin.brands.index')
                ->with('success', 'Thương hiệu đã được cập nhật thành công.');
        }

        // Nếu là nhân viên, tạo yêu cầu phê duyệt
        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action' => 'update',
                'model_type' => 'brand',
                'model_id' => $brand->id,
                'payload' => $validated,
                'status' => 'pending',
                'admin_id' => null,
                'approved_at' => null,
            ]);

            return redirect()->route('admin.brands.index')
                ->with('success', 'Yêu cầu chỉnh sửa thương hiệu đã được gửi, chờ admin phê duyệt.');
        }

        // Nếu không phải admin hoặc nhân viên, từ chối
        abort(403);
    }
}
