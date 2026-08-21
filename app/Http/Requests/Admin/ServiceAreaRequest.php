<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $areaId = $this->route('serviceArea')?->id;

        return [
            'zip' => ['required', 'string', 'max:20', 'unique:service_areas,zip'.($areaId ? ",{$areaId}" : '')],
            'area' => ['required', 'string', 'max:160'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
