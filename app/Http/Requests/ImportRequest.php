<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ? $this->route('id') : "";
        return [
            'supplier' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255|unique:imports,name,' . $id,
            'import_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',

            // Product rules
            'product.*.total_price' => 'required|numeric|min:0',

            // Variant rules
            'variant-product.*.product_variant_id' => 'required|exists:product_variants,id',
            'variant-product.*.quantity' => 'required|integer|min:1',
            'variant-product.*.price_per_unit' => 'required|numeric|min:0',
            'variant-product.*.expected_price' => 'required|numeric|min:0',
            'variant-product.*.total_price' => 'required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'supplier.required' => 'Please select a supplier',
            'name.required' => 'Import name is required',
            'import_date.required' => 'Import date is required',
            'total_amount.required' => 'Total amount is required'
        ];
    }
}
