<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SeoAnalyticsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ga4_id' => ['nullable', 'string', 'max:32', 'regex:/^G-[A-Z0-9]+$/'],
            'yandex_metrika_id' => ['nullable', 'digits_between:1,10'],
            'robots_txt' => ['nullable', 'string', 'max:2000'],
            'custom_head_code' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
