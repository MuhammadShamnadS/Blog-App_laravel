<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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

            'username'   => 'required|string|min:2|max:255|unique:users',
            'name' => 'required|string|min:2|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username is required',
            'username.string' => 'Username must be a string',
            'username.min' => 'Username must be minimum of 2 characters',
            'username.max' => 'Username must be maximum of 255 characters',
            'username.unique' => 'Username already exists, Please choose a different one',
            'name.required' => 'Please enter your name',
            'name.string' => 'Name must be string',
            'name.min' => 'Name must be of 2 characters',
            'name.max' => 'Name must be of 255 characters',
            'email.required' => 'Please enter your email',
            'email.email' => 'Please check your email format',
            'email.unique' => 'An acount with this email exists',
            'password.required' => 'Password cannot be empty',
            'password.min' => 'Password must be atleast 6 characters',
        ];
    }
}
