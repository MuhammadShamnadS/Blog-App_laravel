<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleChangeRequest extends FormRequest
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
            'requested_role' => 'required|in:author,editor',
        ];
    }
    public function messages(): array
    {
        return [
            'requested_role.required' => 'Please select a role',
            'requested_role.in' => 'Please select a valid role',
        ];
    }
}
