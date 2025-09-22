<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPostFeatureRequest extends FormRequest
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
            'featured' => 'required|boolean',

        ];
    }

    public function messages()
    {
        return
            [
                'featured.required' => 'The featured field is required.',
                'featured.boolean'  => 'The featured field must be true or false.',


            ];
    }
}
