<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantAttributeRequest extends FormRequest
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
            'id_product_variant' => 'required|exists:product_variants,id',
            'id_attribute_value' => 'required|exists:attribute_values,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id_product_variant.required' => 'Trường id_product_variant là bắt buộc.',
            'id_product_variant.exists' => 'id_product_variant không hợp lệ.',
            'id_attribute_value.required' => 'Trường id_attribute_value là bắt buộc.',
            'id_attribute_value.exists' => 'id_attribute_value không hợp lệ.',
        ];
    }
}
