<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ListReviewsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:160'],
            'status' => ['sometimes', 'required', 'string', 'in:published,hidden'],
            'rating' => ['sometimes', 'required', 'integer', 'between:1,5'],
        ];
    }
}
