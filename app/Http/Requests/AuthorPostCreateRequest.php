<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorPostCreateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'title'       => 'required|string|min:2|max:255',
            'content'     => 'required|string|min:2',
            'category'    => 'required|string|exists:categories,name',
            'tags'        => 'required|array|exists:tags,name',
            'media.*'     => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mp3,pdf,docx|max:20480',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for your post.',
            'title.string'   => 'The title must be a valid text.',
            'title.min'      => 'The title must be at least 2 characters long.',
            'title.max'      => 'The title cannot exceed 255 characters.',
            'content.required' => 'Content is required for your post.',
            'content.string'   => 'The content must be valid text.',
            'content.min'      => 'The content must be at least 2 characters long.',
            'category.required' => 'Please select a category.',
            'category.string'   => 'Category must be valid text.',
            'category.exists'       => 'The selected category is invalid.',
            'tags.array'     => 'Tags must be provided as a list.',
            'tags.required'     => 'Tags must be provided.',
            'tags.exists' => 'Tag is invalid',
            'media.*.file'    => 'Each media item must be a valid file.',
            'media.*.mimes'   => 'Allowed media formats are: jpg, jpeg, png, gif, mp4, mp3, pdf, docx.',
            'media.*.max'     => 'Each media file may not be larger than 20MB.',
        ];
    }
}
