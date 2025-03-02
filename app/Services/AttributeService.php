<?php

namespace App\Services;

use App\Models\Attribute;
use Illuminate\Support\Facades\Validator;

class AttributeService
{
    public function __construct()
    {
        // Constructor logic
    }
    public function getAttributes()
    {
        return Attribute::all();
    }
    public function getAttributeById($id)
    {
        $attribute = Attribute::with('values')->find($id);

        if (!$attribute) {
            return null;
        }
        $data = [
            'attribute_id' => $attribute->id,
            'values' => $attribute->values->pluck('value', 'id')
        ];
        return $data;
    }

    public function createAttribute($data)
    {
        try {
            $validatedData = Validator::make($data, [
                'name' => 'required|string|max:255|unique:attributes,name',
            ])->validated();
            $attribute = Attribute::create($validatedData);
            return $attribute;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateAttribute($id, $data)
    {
        $attribute = Attribute::find($id);
        if (!$attribute) {
            return null;
        }
        $attribute->update($data);
        return $attribute;
    }
    public function deleteAttribute($id)
    {
        $attribute = Attribute::find($id);
        if ($attribute) {
            $attribute->delete();
            return true;
        }
        return false;
    }
    public function createValue($attributeId, $value)
    {
        $attribute = Attribute::find($attributeId);
        if (!$attribute) {
            return null;
        }
        $newValue = $attribute->values()->create(['value' => $value]);
        $data = [
            'attribute_id' => $attribute->id,
            'value' => $newValue->value
        ];
        return $data;
    }
    public function updateValue($attributeId, $valueId, $newValue)
    {
        $attribute = Attribute::find($attributeId);
        if (!$attribute) {
            return null;
        }
        $value = $attribute->values()->find($valueId);
        if (!$value) {
            return null;
        }
        $value->update(['value' => $newValue]);
        return $value;
    }
    public function deleteValue($attributeId, $valueId)
    {
        $attribute = Attribute::find($attributeId);
        if (!$attribute) {
            return null;
        }
        $value = $attribute->values()->find($valueId);
        if (!$value) {
            return null;
        }
        $value->delete();
        return $value;
    }
}
