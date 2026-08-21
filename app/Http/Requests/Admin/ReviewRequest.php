<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:160'],
            'author_avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:160'],
            'content' => ['required', 'string'],
            'review_date' => ['nullable', 'date'],
            'published' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
        ];
    }
}
