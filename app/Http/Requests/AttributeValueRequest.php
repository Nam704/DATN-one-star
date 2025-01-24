<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_attribute' => 'required|exists:attributes,id', // Phải tồn tại trong bảng attributes
            // 'value' => 'required|string|max:100', // Giá trị không được vượt quá 100 ký tự
            // 'status' => 'required|in:active,inactive', // Chỉ được phép là 'active' hoặc 'inactive'
            'tags' => 'required|array|min:1', // 
            'tags.*' => 'required|string|max:255', // Mỗi phần tử của mảng là chuỗi tối đa 255 ký tự
        ];
    }

    public function messages(): array
    {
        return [
            'id_attribute.required' => 'Thuộc tính không được để trống.',
            'id_attribute.exists' => 'Thuộc tính không hợp lệ.',
            'tags.required' => 'Giá trị thuộc tínhtính không được để trống.',
            'tags.max' => 'Giá trị thuộc tính không được vượt quá 255 ký tự.',
        ];
    }
}
