<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatalogStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:published,hidden'],
            'reason' => ['required_if:status,hidden', 'nullable', 'string', 'max:1000'],
        ];
    }
}
