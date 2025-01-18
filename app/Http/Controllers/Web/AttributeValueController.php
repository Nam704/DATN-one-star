<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attribute_value;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use App\Models\Attribute;


class AttributeValueController extends Controller
{
    public function index()
    {
        $attributeValues = Attribute_value::all();
        return view('admin.attributeValues.index', compact('attributeValues'));
    }

    public function create()
{
    $attributes = Attribute::where('status', 'active')->get();
    return view('admin.attributeValues.create', compact('attributes'));
}


public function store(Request $request)
{
    $validated = $request->validate([
        'id_attribute' => 'required|exists:attributes,id',
        'value' => [
            'required',
            'max:50',
            'unique:attribute_values,value',
            'string'
        ],
        'status' => 'required|in:active,inactive'
    ], [
        'id_attribute.required' => 'The attribute field is required',
        'value.required' => 'The value field must not be empty',
        'value.max' => 'The value must not exceed 50 characters',
        'value.unique' => 'This attribute value already exists',
        'status.required' => 'Please select a status'
    ]);

    Attribute_value::create($validated);

    return redirect()->route('admin.attribute-values.index')
        ->with('success', 'Attribute value created successfully');
}



public function destroy($id)
{
    $attributeValue = Attribute_value::find($id);
    $attributeValue->delete();

    return response()->json([
        'success' => true,
        'message' => 'Attribute value deleted successfully'
    ]);
}

public function trash()
{
    $trashedAttributeValues = Attribute_value::onlyTrashed()->get();
    return view('admin.attributeValues.trash', compact('trashedAttributeValues'));
}

public function restore($id)
{
    $attributeValue = Attribute_value::withTrashed()->find($id);
    $attributeValue->restore();

    return response()->json([
        'success' => true,
        'message' => 'Attribute value restored successfully'
    ]);
}

public function forceDelete($id)
{
    $attributeValue = Attribute_value::withTrashed()->find($id);
    $attributeValue->forceDelete();

    return response()->json([
        'success' => true,
        'message' => 'Attribute value permanently deleted successfully'
    ]);
}

    public function toggleStatus($id)
    {
        $attributeValue = Attribute_value::findOrFail($id);
        $attributeValue->status = $attributeValue->status === 'active' ? 'inactive' : 'active';
        $attributeValue->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'newStatus' => $attributeValue->status,
            'id' => $attributeValue->id
        ]);
    }

    public function edit($id)
{
    $attributeValue = Attribute_value::findOrFail($id);
    $attributes = Attribute::where('status', 'active')->get();
    return view('admin.attributeValues.edit', compact('attributeValue', 'attributes'));
}


public function update(Request $request, $id)
{
    $attributeValue = Attribute_value::findOrFail($id);
    
    $validated = $request->validate([
        'id_attribute' => 'required|exists:attributes,id',
        'value' => [
            'required',
            'max:50',
            'string',
            Rule::unique('attribute_values')->ignore($id)
        ],
        'status' => 'required|in:active,inactive'
    ], [
        'id_attribute.required' => 'The attribute field is required',
        'value.required' => 'The value field must not be empty',
        'value.max' => 'The value must not exceed 50 characters',
        'value.unique' => 'This attribute value already exists',
        'status.required' => 'Please select a status'
    ]);

    $attributeValue->update($validated);

    return redirect()->route('admin.attribute-values.index')
        ->with('success', 'Attribute value updated successfully');
}

public function getAttributeValues($id)
{
    $values = AttributeValue::where('id_attribute', $id)
        ->where('status', 'active')
        ->get();
    return response()->json($values);
}


}
