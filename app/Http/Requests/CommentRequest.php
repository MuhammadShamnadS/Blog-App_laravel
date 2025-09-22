<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
            'post_id'   => 'required|integer|exists:posts,id',
            'parent_id' => 'nullable|integer|exists:comments,id',
            'content'   => 'required|string|max:2000',
            'status'    => 'sometimes|in:0,1',
        ];
    }


    public function messages()
    {
        return
            [
                'post_id.required' => 'Post is required',
                'post_id.integer' => 'Post id must be an integer',
                'post_id.exists' => 'Post not exist',
                'content.required' => 'Comment is required',
                'content.string' => 'Comment must be characters',
                'content.max' => 'Comment must be maximum of 2000 characters',
                'parent_id.integer' => 'Parent id must be an integer',
                'parent_id.exists' => 'Parent id must exists',
                'status.in' => 'Status must be in spam or delete'

            ];
    }
}
