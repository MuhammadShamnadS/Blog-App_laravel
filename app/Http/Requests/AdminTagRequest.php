<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminTagRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:20'
        ];
    }

    public function messages()
    {
        return 
        [
            'name.required' => 'Please provide a tag name',
            'name.string' => 'Invalid input format',
            'name.min' => 'Tag must be minimum of length 2',
            'name.max' => 'Tag must be maximum of length 20'
        ];
    }
}
