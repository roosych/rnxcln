<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class YelpSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'api_key' => ['required', 'string', 'max:255'],
            'business_id' => ['required', 'string', 'max:255'],
            'business_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
