<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ListProvidersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'required', 'string', 'in:pending,approved,rejected,all'],
            'account_status' => ['sometimes', 'required', 'string', 'in:active,suspended'],
        ];
    }
}
