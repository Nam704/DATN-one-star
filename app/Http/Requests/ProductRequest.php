<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $productId=$this->route('id');
        return [
            'name' => 'required|string|max:255|unique:products,name'.$productId,
            'id_brand' => 'required',
            'id_category' => 'required',
            'image_primary' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
        'name.required' => 'Tên sản phẩm không được bỏ trống',
        'name.unique' => 'Tên sản phẩm đã tồn tại',
        'id_branch.required' => 'Vui lòng chọn thương hiệu.',
        'id_category.required' => 'Vui lòng chọn danh mục.',
        'image_primary.required' => 'Vui lòng tải lên hình ảnh sản phẩm.',
        'image_primary.image' => 'Tệp tải lên phải là hình ảnh.',
        'image_primary.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, hoặc gif.',
        'image_primary.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        'status.required' => 'Trạng thái sản phẩm là bắt buộc.',
        'status.in' => 'Trạng thái phải là "active" hoặc "inactive".',
        ];
    }
}