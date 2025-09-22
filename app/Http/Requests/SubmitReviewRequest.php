<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitReviewRequest extends FormRequest
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
            'status'   => 'required|in:approved,rejected',
            'feedback' => 'nullable|string|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'Please approve or reject',
            'status.in' => 'Allowed only to approve or reject post',
            'feedback.string' => 'Invalid feedback format',
            'feedback.max' => 'Feedback must be maximum 2000 characters'
        ];
    }
}
