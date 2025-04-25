<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Attribute_value;
use App\Models\RequestModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttributeValueController extends Controller
{
    public function list()
    {
        $attributes_value = Attribute_value::with('attribute')->latest()->get();
        return view('admin.attribute_value.list', compact('attributes_value'));
    }

    public function add()
    {
        $attributes = Attribute::withoutTrashed()->get(['id', 'name']);
        return view('admin.attribute_value.add', compact('attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_attribute' => ['required', 'exists:attributes,id'],
            'value' => [
                'required',
                'max:100',
                Rule::unique('attribute_values', 'value')
                    ->where(fn($q) => $q->where('id_attribute', $request->id_attribute))
            ],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'id_attribute.required' => 'Vui lòng chọn thuộc tính',
            'id_attribute.exists' => 'Thuộc tính không hợp lệ',
            'value.required' => 'Giá trị không được để trống',
            'value.max' => 'Giá trị không được vượt quá 100 ký tự',
            'value.unique' => 'Giá trị đã tồn tại cho thuộc tính này',
            'status.required' => 'Vui lòng chọn trạng thái',
        ]);

        if (auth()->user()->isAdmin()) {
            Attribute_value::create($validated);
            return redirect()->route('admin.attribute_values.list')
                ->with('success', 'Tạo giá trị thuộc tính thành công');
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action' => 'tạo giá trị thuộc tính',
                'model_type' => 'giá trị thuộc tính',
                'payload' => $validated,
                'status' => 'pending',
            ]);
            return redirect()->route('admin.attribute_values.list')
                ->with('info', 'Yêu cầu tạo giá trị thuộc tính đã được gửi, chờ quản trị viên phê duyệt.');
        }

        return redirect()->route('admin.attribute_values.list')
            ->with('error', 'Bạn không có quyền thực hiện hành động này.');
    }

    public function edit($id)
    {
        $value = Attribute_value::findOrFail($id);
        $attributes = Attribute::withoutTrashed()->pluck('name', 'id');
        return view('admin.attribute_value.update', compact('value', 'attributes'));
    }

    public function update(Request $request, $id)
    {
        $valueModel = Attribute_value::findOrFail($id);
        $validated = $request->validate([
            'id_attribute' => ['required', 'exists:attributes,id'],
            'value' => [
                'required',
                'max:100',
                Rule::unique('attribute_values', 'value')
                    ->where(fn($q) => $q->where('id_attribute', $request->id_attribute))
                    ->ignore($id)
            ],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'id_attribute.required' => 'Vui lòng chọn thuộc tính',
            'id_attribute.exists' => 'Thuộc tính không hợp lệ',
            'value.required' => 'Giá trị không được để trống',
            'value.max' => 'Giá trị không được vượt quá 100 ký tự',
            'value.unique' => 'Giá trị đã tồn tại cho thuộc tính này',
            'status.required' => 'Vui lòng chọn trạng thái',
        ]);

        if (auth()->user()->isAdmin()) {
            $valueModel->update($validated);
            return redirect()->route('admin.attribute_values.list')
                ->with('success', 'Cập nhật giá trị thuộc tính thành công');
        }

        if (auth()->user()->isEmployee()) {
            RequestModel::create([
                'employee_id' => auth()->id(),
                'action' => 'thay đổi giá trị thuộc tính',
                'model_type' => 'giá trị thuộc tính',
                'model_id' => $valueModel->id,
                'payload' => $validated,
                'status' => 'pending',
            ]);
            return redirect()->route('admin.attribute_values.list')
                ->with('info', 'Yêu cầu cập nhật giá trị thuộc tính đã được gửi, chờ quản trị viên phê duyệt.');
        }

        return redirect()->route('admin.attribute_values.list')
            ->with('error', 'Bạn không có quyền thực hiện hành động này.');
    }

    public function destroy($id)
    {
        try {
            $valueModel = Attribute_value::findOrFail($id);

            if (auth()->user()->isAdmin()) {
                $valueModel->delete();
                return redirect()->route('admin.attribute_values.list')
                    ->with('success', 'Xóa giá trị thành công');
            }

            if (auth()->user()->isEmployee()) {
                RequestModel::create([
                    'employee_id' => auth()->id(),
                    'action' => 'xóa giá trị thuộc tính',
                    'model_type' => 'giá trị thuộc tính',
                    'model_id' => $valueModel->id,
                    'payload' => [
                        'value' => $valueModel->value,
                        'id_attribute' => $valueModel->id_attribute,
                        'status' => $valueModel->status
                    ],
                    'status' => 'pending',
                ]);
                return redirect()->route('admin.attribute_values.list')
                    ->with('info', 'Yêu cầu xóa giá trị đã được gửi, chờ quản trị viên phê duyệt.');
            }

            return redirect()->route('admin.attribute_values.list')
                ->with('error', 'Bạn không có quyền thực hiện hành động này.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.attribute_values.list')
                ->with('error', 'Giá trị thuộc tính không tồn tại hoặc đã bị xóa.');
        }
    }

    public function trash()
    {
        $trashed = Attribute_value::onlyTrashed()->get();
        return view('admin.attribute_value.trash', compact('trashed'));
    }

    public function restore($id)
    {
        try {
            $valueModel = Attribute_value::withTrashed()->findOrFail($id);

            if (auth()->user()->isAdmin()) {
                $valueModel->restore();
                return redirect()->route('admin.attribute_values.list')
                    ->with('success', 'Khôi phục giá trị thành công');
            }

            if (auth()->user()->isEmployee()) {
                RequestModel::create([
                    'employee_id' => auth()->id(),
                    'action' => 'khôi phục giá trị thuộc tính',
                    'model_type' => 'giá trị thuộc tính',
                    'model_id' => $valueModel->id,
                    'payload' => [
                        'value' => $valueModel->value,
                        'id_attribute' => $valueModel->id_attribute,
                        'status' => $valueModel->status
                    ],
                    'status' => 'pending',
                ]);
                return redirect()->route('admin.attribute_values.trash')
                    ->with('info', 'Yêu cầu khôi phục giá trị đã được gửi, chờ quản trị viên phê duyệt.');
            }

            return redirect()->route('admin.attribute_values.trash')
                ->with('error', 'Bạn không có quyền thực hiện hành động này.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.attribute_values.trash')
                ->with('error', 'Giá trị thuộc tính không tồn tại.');
        }
    }

    public function forceDelete($id)
    {
        try {
            $valueModel = Attribute_value::withTrashed()->findOrFail($id);

            if (!auth()->user()->isAdmin()) {
                return redirect()->route('admin.attribute_values.trash')
                    ->with('error', 'Chỉ quản trị viên mới có quyền xóa vĩnh viễn.');
            }

            $valueModel->forceDelete();
            return redirect()->route('admin.attribute_values.trash')
                ->with('success', 'Xóa vĩnh viễn giá trị thành công');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.attribute_values.trash')
                ->with('error', 'Giá trị thuộc tính không tồn tại.');
        }
    }

    public function toggleStatus($id)
    {
        try {
            $valueModel = Attribute_value::findOrFail($id);

            if (auth()->user()->isAdmin()) {
                $newStatus = $valueModel->status === 'active' ? 'inactive' : 'active';
                $valueModel->update(['status' => $newStatus]);
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thành công',
                    'action' => 'updated',
                    'newStatus' => $newStatus
                ]);
            }

            if (auth()->user()->isEmployee()) {
                RequestModel::create([
                    'employee_id' => auth()->id(),
                    'action' => 'thay đổi trạng thái thuộc tính',
                    'model_type' => 'giá trị thuộc tính',
                    'model_id' => $valueModel->id,
                    'payload' => [
                        'value' => $valueModel->value,
                        'id_attribute' => $valueModel->id_attribute,
                        'status' => $valueModel->status === 'active' ? 'inactive' : 'active'
                    ],
                    'status' => 'pending',
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Yêu cầu cập nhật trạng thái đã được gửi, chờ quản trị viên phê duyệt.',
                    'action' => 'requested'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Giá trị thuộc tính không tồn tại hoặc đã bị xóa.'
            ], 404);
        }
    }
}
