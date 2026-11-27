<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ListBookingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:32'],
            'status' => ['sometimes', 'required', 'string', 'max:255'],
            'provider_id' => ['sometimes', 'required', 'integer', 'min:1'],
            'date_from' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'date_to' => [
                'sometimes',
                'required',
                'date_format:Y-m-d',
                ...($this->filled('date_from') ? ['after_or_equal:date_from'] : []),
            ],
        ];
    }
}
