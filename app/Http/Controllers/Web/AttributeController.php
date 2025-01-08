<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
class AttributeController extends Controller
{

/* Danh sách thuộc tính */

public function index()
{
    $attributes = Attribute::all();
    return view('admin.attributes.index', compact('attributes'));
}


/* Tạo thuộc tính */

    public function create()
    {
        return view('admin.attributes.create');
    }

/* Lưu thuộc tính */

public function store(Request $request)
{
    // Thực hiện kiểm tra tính hợp lệ của dữ liệu đầu vào
    $validated = $request->validate([
        'name' => [
            'required',                // Trường 'name' là bắt buộc
            'max:50',                  // Giới hạn độ dài của 'name' không quá 50 ký tự
            'unique:attributes,name',  // Đảm bảo 'name' không trùng lặp trong bảng 'attributes'
            'regex:/^[\p{L}\s]+$/u',// Chỉ cho phép chữ cái
            'string'                   // Dữ liệu phải là kiểu chuỗi
        ],
        'description' => 'nullable|string',       // Trường 'description' không bắt buộc, nhưng nếu có phải là chuỗi
        'status' => 'required|in:active,inactive' // Trường 'status' là bắt buộc và chỉ được nhận 'active' hoặc 'inactive'
    ], [
        // Các thông báo lỗi tùy chỉnh
        'name.required' => 'The name field must not be empty', // Thông báo khi thiếu trường 'name'
        'name.max' => 'The name must not exceed 50 characters',    // Thông báo khi 'name' quá dài
        'name.unique' => 'This attribute name already exists',    // Thông báo khi 'name' bị trùng
        'name.regex' => 'Only letters are allowed in the name', // Thông báo khi 'name' chứa ký tự không hợp lệ
        'status.required' => 'Please select a status' // Thông báo khi thiếu trường 'status'
    ]);

    // Tạo mới một thuộc tính với dữ liệu đã được xác thực
    Attribute::create($validated);

    // Chuyển hướng người dùng về trang danh sách thuộc tính với thông báo thành công
    return redirect()->route('admin.attributes.index')
        ->with('success', 'Attribute created successfully');
}

/* Xóa thuộc tính */

public function destroy($id)
{
    $attribute = Attribute::find($id);
    $attribute->delete();

    return response()->json([
        'success' => true,
        'message' => 'Attribute deleted successfully'
    ]);
}



public function trash()
{
    $trashedAttributes = Attribute::onlyTrashed()->get();
    return view('admin.attributes.trash', compact('trashedAttributes'));
}

public function restore($id)
{
    $attribute = Attribute::withTrashed()->find($id);
    $attribute->restore();

    return response()->json([
        'success' => true,
        'message' => 'Attribute restored successfully'
    ]);
}

public function forceDelete($id)
{
    $attribute = Attribute::withTrashed()->find($id);
    $attribute->forceDelete(); // Permanent delete

    return response()->json([
        'success' => true,
        'message' => 'Attribute permanently deleted successfully'
    ]);
}
public function toggleStatus($id)
{
    $attribute = Attribute::findOrFail($id);
    $attribute->status = $attribute->status === 'active' ? 'inactive' : 'active';
    $attribute->save();

    return response()->json([
        'success' => true,
        'message' => 'Status updated successfully',
        'newStatus' => $attribute->status,
        'id' => $attribute->id
    ]);
}



/* Cập nhật thuộc tính */

public function edit($id)
{
    $attribute = Attribute::findOrFail($id);
    return view('admin.attributes.edit', compact('attribute'));
}

public function update(Request $request, $id)
{
    $attribute = Attribute::findOrFail($id);
    
    $validated = $request->validate([
        'name' => [
            'required',
            'max:50',
            'regex:/^[\p{L}\s]+$/u',
            'string',
            Rule::unique('attributes')->ignore($id)
        ],
        'description' => 'nullable|string',
        'status' => 'required|in:active,inactive'
    ], [
        'name.required' => 'Trường tên không được để trống',
        'name.max' => 'Tên không được vượt quá 50 ký tự',
        'name.regex' => 'Chỉ được phép có chữ cái trong tên',
        'name.unique' => 'Tên thuộc tính này đã tồn tại',
        'status.required' => 'Vui lòng chọn một trạng thái'
    ]);

    $attribute->update($validated);

    return redirect()->route('admin.attributes.index')
        ->with('success', 'Attribute updated successfully');
}


}
