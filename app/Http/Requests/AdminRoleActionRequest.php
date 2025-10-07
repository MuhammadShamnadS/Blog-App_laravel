<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRoleActionRequest extends FormRequest
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
            'action' => 'required|in:approve,reject',
            'category_id' => 'nullable|integer|exists:categories,id',

        ];
    }

    public function messages()
    {
        return [
            'action.required' => 'Action is required',
            'action.in' => 'Actions must be approve or reject',
            'category_id.integer' => 'Category id must be an integer',
            'category_id.exists' => 'Category not exists'
        ];
    }
}
