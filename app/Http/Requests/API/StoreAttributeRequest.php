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
                'regex:/^[a-zA-Z]+$/',
                'string'
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field must not be empty',
            'name.max' => 'The name must not exceed 50 characters',
            'name.unique' => 'This attribute name already exists',
            'name.regex' => 'Only letters are allowed in the name',
            'status.required' => 'Please select a status'
        ];
    }
}
