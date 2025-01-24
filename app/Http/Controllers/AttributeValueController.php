<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attribute_value;
use App\Models\Attribute;
use App\Http\Requests\AttributeValueRequest;

class AttributeValueController extends Controller
{
    public function index()
    {
        $attributes_value = Attribute_value::with('attribute')->orderBy('id', 'desc')->get();
        return view('admin.attribute_value.list', compact('attributes_value'));
    }

    public function create()
    {
        $attributes = Attribute::get();
        return view('admin.attribute_value.add', compact('attributes'));
    }

    public function store(AttributeValueRequest $request)
    {
        $tags = $request->input('tags', []); // Mặc định là mảng rỗng nếu không có dữ liệu

        if (empty($tags)) {
            return back()->with('error', 'Tags không được để trống.');
        }

        foreach ($tags as $tag) {
            $exists = Attribute_value::where('id_attribute', $request->id_attribute)
                ->where('value', $tag)
                ->exists();

            if ($exists) {
                return back()->with('message_error', "Giá trị thuộc tính '{$tag}' đã tồn tại cho thuộc tính được chọn.");
            }

            $data = [
                'id_attribute' => $request->id_attribute,
                'value' => $tag,
                'status' => $request->status,
                'updated_at' => $request->updated_at,
            ];

            Attribute_value::create($data);
        }

        return redirect()->route('admin.attribute_values.index')->with([
            'message' => 'Thêm giá trị thuộc tính thành công!',
        ]);
    }


    public function show(string $id)
    {
        // 
    }

    public function edit(string $id)
    {
        $attributes = Attribute::all();
        $attributes_value = Attribute_value::with('attribute')->find($id);
        return view('admin.attribute_value.update', compact('attributes', 'attributes_value'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'id_attribute' => 'required',
            'value' => 'required|string|max:255',
            'status_value' => 'required',
        ]);

        $exists = Attribute_value::where('id_attribute', $request->id_attribute)
            ->where('value', $request->value)
            ->where('id', '!=', $id) // Loại trừ bản ghi hiện tại
            ->exists();

        if ($exists) {
            return back()->with('message_error', "Giá trị thuộc tính '{$request->value}' đã tồn tại cho thuộc tính được chọn.");
        }

        $attributes_value = Attribute_value::find($id);
        $attributes_value->update([
            'id_attribute' => $request->id_attribute,
            'value' => $request->value,
            'status' => $request->status_value,
        ]);

        return redirect()->route('admin.attribute_values.index')->with([
            'message' => 'Cập nhật giá trị thuộc tính thành công!',
        ]);
    }

    public function destroy(string $id)
    {
        $attributes_value = Attribute_value::find($id);
        $attributes_value->delete();

        return redirect()->route('admin.attribute_values.index')->with([
            'message' => 'Xóa giá trị thuộc tính thành công!',
        ]);
        ;
    }

    public function trash()
    {
        $trash_attributes_value = Attribute_value::onlyTrashed()->get();
        return view('admin.attribute_value.trash', compact('trash_attributes_value'));
    }

    public function restore(string $id)
    {
        $attributes_value = Attribute_value::withTrashed()->find($id);
        $attributes_value->restore();

        return redirect()->route('admin.attribute_values.index')->with([
            'message' => 'Khôi phục giá trị thuộc tính thành công!',
        ]);
        ;
    }



}
