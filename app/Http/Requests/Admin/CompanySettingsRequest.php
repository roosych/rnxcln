<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CompanySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'phone_e164' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:160'],
            'address_city' => ['required', 'string', 'max:120'],
            'address_line_1' => ['required', 'string', 'max:160'],
            'address_line_2' => ['required', 'string', 'max:160'],
            'stats_jobs' => ['required', 'integer'],
            'stats_years' => ['required', 'integer'],
            'stats_since' => ['required', 'integer'],
            'stats_rating' => ['required', 'string', 'max:20'],
            'footer_seo_text' => ['required', 'string', 'max:200'],
            'logo_dark' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'logo_light' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,ico,svg', 'max:512'],
        ];
    }
}
