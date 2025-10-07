<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeAuthorsRequest extends FormRequest
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
            'author_id' => 'required|exists:users,id',
        ];
    }


    public function messages()
    {
        return [
            'author_id.required' => 'Please provide the author id',
            'author_id.exists' => 'Author not exists'
        ];
    }
}
