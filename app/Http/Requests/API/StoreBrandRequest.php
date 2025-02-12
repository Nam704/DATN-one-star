<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
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
                'unique:brands,name',
                'regex:/^[\p{L}\s]+$/u',
                'string'
            ],
            'status' => 'required|in:active,inactive'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên thương hiệu không được để trống',
            'name.max' => 'Tên thương hiệu không được vượt quá 50 ký tự',
            'name.unique' => 'Tên thương hiệu đã tồn tại',
            'status.required' => 'Trạng thái không được để trống',
            'status.in' => 'Trạng thái không hợp lệ'
        ];
    }
}
