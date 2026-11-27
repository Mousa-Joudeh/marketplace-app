<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ListPaymentsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:64'],
            'method' => ['sometimes', 'required', 'string', 'in:cash,cliq,stripe'],
            'status' => ['sometimes', 'required', 'string', 'max:255'],
            'transaction_type' => ['sometimes', 'required', 'string', 'in:charge,refund'],
        ];
    }
}
