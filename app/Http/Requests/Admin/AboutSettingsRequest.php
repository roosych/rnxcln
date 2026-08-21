<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AboutSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_title' => ['required', 'string', 'max:120'],
            'stats_caption' => ['required', 'string', 'max:200'],
            'years_caption' => ['required', 'string', 'max:200'],
            'since_prefix' => ['required', 'string', 'max:120'],
            'safe_headline' => ['required', 'string', 'max:200'],
            'safe_body' => ['required', 'string', 'max:400'],
        ];
    }
}
