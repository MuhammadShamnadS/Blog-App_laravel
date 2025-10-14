<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $categoryId = $this->route('id');
        return [

            'name' => [
                'required',
                'string',
                'min:2',
                'max:20',
                Rule::unique('categories')->ignore($categoryId),
            ],

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Category name is required.',
            'name.string'   => 'Category name must be a valid string.',
            'name.min'      => 'Category name must be at least 2 characters long.',
            'name.max'      => 'Category name must not exceed 20 characters.',
            'name.unique'   => 'Category already exists'
        ];
    }
}
