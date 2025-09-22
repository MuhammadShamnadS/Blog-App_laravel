<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPostScheduleRequest extends FormRequest
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
            'schedule_at' => 'required|date|after:now',

        ];
    }
    public function messages()
    {
        return
            [
                'schedule_at.required' => 'Please choose a schedule date',
                'schedule_at.date' => 'Please choose a valid date format',
                'schedule_at.after' => "Please choose a upcoming time"
            ];
    }
}
