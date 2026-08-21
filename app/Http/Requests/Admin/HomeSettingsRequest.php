<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomeSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hero_line_1' => ['required', 'string', 'max:200'],
            'hero_line_2' => ['required', 'string', 'max:500'],
            'hero_line_3' => ['required', 'string', 'max:500'],
            'hero_lead' => ['required', 'string', 'max:400'],
            'section_1_title' => ['required', 'string', 'max:160'],
            'section_1_lead' => ['required', 'string', 'max:600'],
            'section_2_title' => ['required', 'string', 'max:160'],
            'section_3_title' => ['required', 'string', 'max:160'],
            'section_3_lead' => ['required', 'string', 'max:600'],
        ];
    }
}
