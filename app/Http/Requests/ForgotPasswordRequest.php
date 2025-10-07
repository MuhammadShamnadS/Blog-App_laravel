<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',

        ];
    }

    public function messages()
    {
        return
            [
                'email.required' => 'Email is required',
                'email.email' => 'Email format is incorrect',
                'email.exists' => 'No account found with this email'
            ];
    }
}
