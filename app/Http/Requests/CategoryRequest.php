<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryID=$this->route('id');
        return [
            'name' => 'required|max:255|unique:categories,name' .$categoryID,
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Không được để trống',
            'name.max' => 'Tên quá dài',
            'name.unique' => 'Danh mục này đã tồn tại',
            'status.required' => 'Không được bỏ trống',
        ];
    }
}
