<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\Admin\ProcessStepController;
use Illuminate\Foundation\Http\FormRequest;

class ProcessStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Titles render unescaped ({!! !!}) wherever they're shown, public pages
     * included, so a <br> can be used to control the line wrap — that's the
     * only tag allowed through. Anything else (script, img onerror, etc.)
     * is stripped here rather than trusting every render site.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $this->merge(['title' => strip_tags((string) $this->input('title'), '<br>')]);
        }
    }

    public function rules(): array
    {
        return [
            'group' => ['required', 'in:'.implode(',', ProcessStepController::GROUPS)],
            'title' => ['required', 'string', 'max:200'],
            'text' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
