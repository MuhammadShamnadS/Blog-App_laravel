<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCategoryRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:20',

        ];
    }

    public function messages()
{
    return [
        'name.required' => 'Name is required.',
        'name.string'   => 'Name must be a valid string.',
        'name.min'      => 'Name must be at least 2 characters long.',
        'name.max'      => 'Name must not exceed 20 characters.',
    ];
}

}
