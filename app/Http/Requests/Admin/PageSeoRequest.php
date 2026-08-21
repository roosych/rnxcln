<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PageSeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meta_title' => ['required', 'string', 'max:200'],
            'meta_description' => ['required', 'string', 'max:300'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'robots' => ['required', 'in:index,noindex'],
            'og_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'remove_og_image' => ['nullable', 'boolean'],
            'schema_json' => ['nullable', 'json'],
        ];
    }
}
