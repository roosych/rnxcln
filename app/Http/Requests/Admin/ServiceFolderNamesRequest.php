<?php

namespace App\Http\Requests\Admin;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class ServiceFolderNamesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return collect(array_keys(Service::DEFAULT_FOLDERS))
            ->mapWithKeys(fn (string $key) => [$key => ['required', 'string', 'max:60']])
            ->all();
    }
}
