<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'service' => ['required', 'string', 'max:120'],
            'zip' => ['required', 'string', 'max:20'],
            'preferred_date' => ['required', 'string', 'max:40'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
