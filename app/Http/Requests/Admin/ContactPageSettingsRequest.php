<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ContactPageSettingsRequest extends FormRequest
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
            'form_heading' => ['required', 'string', 'max:200'],
            'section_1_title' => ['required', 'string', 'max:160'],
            'section_2_title' => ['required', 'string', 'max:160'],
        ];
    }
}
