<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
        $id = $this->route('id') ? $this->route('id') : "";

        return [
            'name' => 'required|string|max:100|unique:suppliers,name,' . $id,
            'status' => 'required|in:active,inactive',
            'address_detail' => 'required|string|max:500',
            'phone' => ['required', 'string', 'regex:/^(0|\+84)[3-9][0-9]{8}$/', 'max:10'],
            'ward' => 'required',

        ];
    }
    public function messages()
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.string' => 'Phone number must be a string.',
            'phone.regex' => 'Invalid phone number. Please enter a valid format (e.g., 0981234567 or +84981234567).',
            'phone.max' => 'Phone number cannot exceed 10 characters.',

        ];
    }
}
