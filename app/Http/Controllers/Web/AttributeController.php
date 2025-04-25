<?php

namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\RequestModel;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::withoutTrashed()->get();
        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required','max:50','unique:attributes,name','regex:/^[\p{L}\s]+$/u','string'],
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive'
        ],[
            'name.required' => 'Trường tên không được để trống',
            'name.max'      => 'Tên không được vượt quá 50 ký tự',
            'name.unique'   => 'Tên thuộc tính đã tồn tại',
            'name.regex'    => 'Chỉ được phép nhập chữ cái và khoảng trắng',
            'status.required' => 'Vui lòng chọn trạng thái'
        ]);

        if (auth()->user()->isAdmin()) {
            Attribute::create($validated);
            return redirect()->route('admin.attributes.index')
                ->with('success','Tạo thuộc tính thành công');
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'create',
                'model_type'  => 'attribute',
                'payload'     => $validated,
                'status'      => 'pending',
            ]);
            return redirect()->route('admin.attributes.index')
                ->with('info','Yêu cầu tạo thuộc tính đã được gửi, chờ quản trị viên phê duyệt.');
        }

        abort(403, 'Bạn không có quyền thực hiện hành động này.');
    }

    public function edit($id)
    {
        $attribute = Attribute::findOrFail($id);
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);
        $validated = $request->validate([
            'name'        => ['required','max:50','regex:/^[\p{L}\s]+$/u','string',Rule::unique('attributes')->ignore($id)],
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive'
        ],[
            'name.required' => 'Trường tên không được để trống',
            'name.max'      => 'Tên không được vượt quá 50 ký tự',
            'name.regex'    => 'Chỉ được phép nhập chữ cái và khoảng trắng',
            'name.unique'   => 'Tên thuộc tính đã tồn tại',
            'status.required' => 'Vui lòng chọn trạng thái'
        ]);

        if (auth()->user()->isAdmin()) {
            $attribute->update($validated);
            return redirect()->route('admin.attributes.index')
                ->with('success','Cập nhật thuộc tính thành công');
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'update',
                'model_type'  => 'attribute',
                'model_id'    => $attribute->id,
                'payload'     => $validated,
                'status'      => 'pending',
            ]);
            return redirect()->route('admin.attributes.index')
                ->with('info','Yêu cầu cập nhật thuộc tính đã được gửi, chờ quản trị viên phê duyệt.');
        }

        abort(403, 'Bạn không có quyền thực hiện hành động này.');
    }

    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);

        if (auth()->user()->isAdmin()) {
            $attribute->delete();
            return response()->json(['success'=>true,'message'=>'Xóa thuộc tính thành công']);
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'delete',
                'model_type'  => 'attribute',
                'model_id'    => $attribute->id,
                'payload'     => ['name'=>$attribute->name,'description'=>$attribute->description,'status'=>$attribute->status],
                'status'      => 'pending',
            ]);
            return response()->json(['success'=>true,'message'=>'Yêu cầu xóa thuộc tính đã được gửi, chờ quản trị viên phê duyệt.']);
        }

        return response()->json(['success'=>false,'message'=>'Bạn không có quyền thực hiện hành động này.'],403);
    }

    public function trash()
    {
        $trashedAttributes = Attribute::onlyTrashed()->get();
        return view('admin.attributes.trash',compact('trashedAttributes'));
    }

    public function restore($id)
{
    $attribute = Attribute::withTrashed()->findOrFail($id);

    if (auth()->user()->isAdmin()) {
        $attribute->restore();
        return response()->json(['success' => true, 'message' => 'Khôi phục thuộc tính thành công', 'action' => 'restored']);
    }

    if (auth()->user()->isEmployee()) {
        RequestModel::create([
            'employee_id' => auth()->id(),
            'action'      => 'restore',
            'model_type'  => 'attribute',
            'model_id'    => $attribute->id,
            'payload'     => ['name' => $attribute->name, 'description' => $attribute->description, 'status' => $attribute->status],
            'status'      => 'pending',
        ]);
        return response()->json(['success' => true, 'message' => 'Yêu cầu khôi phục thuộc tính đã được gửi, chờ quản trị viên phê duyệt.', 'action' => 'requested']);
    }

    return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.'], 403);
}

    public function forceDelete($id)
{
    $attribute = Attribute::withTrashed()->findOrFail($id);

    if (!auth()->user()->isAdmin()) {
        return response()->json(['success' => false, 'message' => 'Chỉ quản trị viên mới có quyền xóa vĩnh viễn.'], 403);
    }

    $attribute->forceDelete();
    return response()->json(['success' => true, 'message' => 'Xóa vĩnh viễn thuộc tính thành công']);
}

    public function toggleStatus($id)
    {
        $attribute = Attribute::findOrFail($id);

        if (auth()->user()->isAdmin()) {
            $attribute->status = $attribute->status==='active'?'inactive':'active';
            $attribute->save();
            return response()->json(['success'=>true,'message'=>'Cập nhật trạng thái thành công','newStatus'=>$attribute->status,'id'=>$attribute->id]);
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action'      => 'update',
                'model_type'  => 'attribute',
                'model_id'    => $attribute->id,
                'payload'     => ['status'=>$attribute->status],
                'status'      => 'pending',
            ]);
            return response()->json(['success'=>true,'message'=>'Yêu cầu thay đổi trạng thái đã được gửi, chờ quản trị viên phê duyệt.']);
        }

        return response()->json(['success'=>false,'message'=>'Bạn không có quyền thực hiện hành động này.'],403);
    }
}
