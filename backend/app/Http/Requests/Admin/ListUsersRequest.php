<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ListUsersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['sometimes', 'required', 'string', 'in:customer,provider,admin'],
            'status' => ['sometimes', 'required', 'string', 'in:active,suspended'],
        ];
    }
}
