<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPostEditorAssignRequest extends FormRequest
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
            'editor_id' => 'required|exists:editors,id'
        ];
    }

    public function messages()
    {
        return
            [
                'editor_id.required' => 'Editor is required',
                'editor_id.exists' => 'Editor not exists'
            ];
    }
}
