<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantRequest extends FormRequest
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
            'id_product' => 'required|exists:products,id',
            'sku' => 'required|string|max:255',
            'status' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'id_product.required' => 'Sản phẩm không được để trống.',
            'id_product.exists' => 'Sản phẩm không tồn tại trong hệ thống.',
            'sku.required' => 'Mã SKU không được để trống.',
            'sku.string' => 'Mã SKU phải là chuỗi ký tự.',
            'sku.max' => 'Mã SKU không được dài quá 255 ký tự.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.string' => 'Trạng thái phải là chuỗi ký tự.',
        ];
    }
}
