<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ListCategoriesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'required', 'string', 'in:active,inactive'],
            'parent_id' => ['sometimes', 'required', 'regex:/\A(?:root|[1-9][0-9]{0,18})\z/'],
        ];
    }
}
