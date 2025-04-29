<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attribute_value;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\Attribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttributeValueController extends Controller
{

    public function index()
    {
        // Lấy danh sách giá trị thuộc tính chưa xóa
        $attributes_value = Attribute_value::with('attribute')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.attribute_value.list', compact('attributes_value'));
    }

    public function create()
    {
        // Lấy danh sách thuộc tính để chọn
        $attributes = Attribute::select('id', 'name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.attribute_value.add', compact('attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_attribute' => 'required|exists:attributes,id',
            'tags' => 'required|array|min:1',
            'tags.*' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $attributeId = $request->input('id_attribute');
            $tags = $request->input('tags'); // mảng các giá trị

            foreach ($tags as $tag) {
                Attribute_value::create([
                    'id_attribute' => $attributeId,
                    'value' => trim($tag),
                    'status' => 'active',
                ]);
            }

            DB::commit();
            return redirect()
                ->route('admin.attribute_values.index')
                ->with('message', 'Thêm giá trị thuộc tính thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('message_error', 'Có lỗi xảy ra khi thêm giá trị: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $attributes_value = Attribute_value::findOrFail($id);
        $attributes = Attribute::select('id', 'name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.attribute_value.update', compact('attributes_value', 'attributes'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_attribute' => 'required|exists:attributes,id',
            'value' => 'required|string|max:255',
            'status_value' => 'required|in:active,inactive',
        ]);

        try {
            $attributesValue = Attribute_value::findOrFail($id);
            $attributesValue->update([
                'id_attribute' => $request->input('id_attribute'),
                'value' => trim($request->input('value')),
                'status' => $request->input('status_value'),
            ]);

            return redirect()
                ->route('admin.attribute_values.index')
                ->with('message', 'Cập nhật giá trị thuộc tính thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message_error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $attributesValue = Attribute_value::findOrFail($id);
            $attributesValue->delete();

            return redirect()
                ->route('admin.attribute_values.index')
                ->with('message', 'Đã chuyển giá trị thuộc tính vào thùng rác.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.attribute_values.index')
                ->with('message_error', 'Xóa thất bại: ' . $e->getMessage());
        }
    }
    public function trash()
    {
        $trash_attributes_value = Attribute_value::onlyTrashed()
            ->with('attribute')
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.attribute_value.trash', compact('trash_attributes_value'));
    }
    public function restore($id)
    {
        try {
            $attributesValue = Attribute_value::onlyTrashed()->findOrFail($id);
            $attributesValue->restore();

            return redirect()
                ->route('admin.attribute_values.trash')
                ->with('message', 'Khôi phục giá trị thuộc tính thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.attribute_values.trash')
                ->with('message_error', 'Khôi phục thất bại: ' . $e->getMessage());
        }
    }
}
