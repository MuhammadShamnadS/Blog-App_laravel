<?php

namespace App\Http\Requests;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class LikeToggleRequest extends FormRequest
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
            'post_id' => 'required|integer|exists:posts,id',
        ];
    }

    public function messages()
    {
        return
            [
                'post_id.required' => 'Please provide a Post',
                'post_id.integer' => 'Post id must be an integer',
                'post_id.exists' => 'Post not exist'
            ];
    }
}
