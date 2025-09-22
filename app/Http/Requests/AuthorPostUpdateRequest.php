<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorPostUpdateRequest extends FormRequest
{

    public function rules()
    {
        return [
            'title'    => 'sometimes|required|string|max:255',
            'content'  => 'sometimes|required|string',
            'category' => 'sometimes|required|string|exists:categories,name',
            'tags'     => 'nullable|array',
            'tags.*'   => 'string|max:50',
            'status'   => 'sometimes|required|in:draft,submitted',
            'media.*'  => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mp3,pdf,docx|max:20480',
        ];
    }


    public function messages()
    {
        return [
            'title.sometimes' => 'Please provide a title for your post.',
            'title.string'   => 'The title must be valid text.',
            'title.max'      => 'The title cannot exceed 255 characters.',
            'content.sometimes' => 'Content is required.',
            'content.string'   => 'The content must be valid text.',
            'category.sometimes' => 'Please select a category.',
            'category.string'   => 'Category must be valid text.',
            'category.exists'      => 'Selected category not available.',
            'tags.array'     => 'Tags must be provided as a list.',
            'tags.*.string'  => 'Each tag must be valid text.',
            'tags.*.max'     => 'Each tag cannot exceed 50 characters.',
            'status.required' => 'Please provide the post status.',
            'status.in'       => 'Status must be either draft or submitted.',
            'media.*.file'   => 'Each media item must be a valid file.',
            'media.*.mimes'  => 'Allowed media formats are: jpg, jpeg, png, gif, mp4, mp3, pdf, docx.',
            'media.*.max'    => 'Each media file may not be larger than 20MB.',
        ];
    }
}
