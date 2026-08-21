<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\Admin\ServiceController;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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

        if (is_array($this->input('steps'))) {
            $this->merge(['steps' => collect($this->input('steps'))->map(function ($step) {
                if (isset($step['title'])) {
                    $step['title'] = strip_tags((string) $step['title'], '<br>');
                }

                return $step;
            })->all()]);
        }
    }

    public function rules(): array
    {
        $serviceId = $this->route('service')?->id;

        return [
            'section' => ['nullable', 'in:'.implode(',', array_keys(Service::DEFAULT_FOLDERS))],
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:160', 'alpha_dash', 'unique:services,slug'.($serviceId ? ",{$serviceId}" : '')],
            'tagline' => ['nullable', 'string', 'max:200'],
            'text' => ['nullable', 'string'],
            'alt' => ['nullable', 'string', 'max:200'],
            'items' => ['nullable', 'string'],
            'link_type' => ['required', 'in:'.implode(',', ServiceController::LINK_TYPES)],
            'link_url' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'before_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'after_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'steps' => ['nullable', 'array'],
            'steps.*.id' => ['nullable', 'integer'],
            'steps.*.title' => ['nullable', 'string', 'max:200'],
            'steps.*.text' => ['nullable', 'string'],
        ];
    }
}
