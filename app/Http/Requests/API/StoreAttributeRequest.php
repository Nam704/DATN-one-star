<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:50',
                'unique:attributes,name',
                'regex:/^[\p{L}\s]+$/u',
                'string'
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Trường tên không được để trống',
            'name.max' => 'Tên không được vượt quá 50 ký tự',
            'name.regex' => 'Chỉ được phép có chữ cái trong tên',
            'name.unique' => 'Tên thuộc tính này đã tồn tại',
            'status.required' => 'Vui lòng chọn một trạng thái'
        ];
    }
}
