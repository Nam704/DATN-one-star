<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
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
    public function rules()
    {
        return [
            'new_password' => [
                'required',
                'string',
                'min:8', // Ít nhất 8 ký tự
                'regex:/[a-z]/', // Phải có chữ thường
                'regex:/[A-Z]/', // Phải có chữ hoa
                'regex:/[0-9]/', // Phải có số
                'regex:/[@$!%*?&#]/', // Phải có ký tự đặc biệt
            ],
            're_password' => [
                'required',
                'same:new_password', // Phải khớp với new-password
            ],
            'password_reset_token' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải ít nhất 8 ký tự.',
            'new_password.regex' => 'Mật khẩu mới phải bao gồm chữ thường, chữ hoa, số và ký tự đặc biệt.',
            're_password.required' => 'Vui lòng nhập lại mật khẩu.',
            're_password.same' => 'Mật khẩu nhập lại không khớp.',
        ];
    }
}
