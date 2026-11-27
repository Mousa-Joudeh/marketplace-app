<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        $presence = $this->isMethod('POST') ? ['required'] : ['sometimes', 'required'];

        return [
            'name' => [...$presence, 'string', 'max:255'],
            'slug' => [...$presence, 'string', 'max:255', 'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/'],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'description' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'icon_key' => ['sometimes', 'nullable', 'string', 'max:120'],
            'sort_order' => ['sometimes', 'required', 'integer', 'between:-2147483648,2147483647'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
