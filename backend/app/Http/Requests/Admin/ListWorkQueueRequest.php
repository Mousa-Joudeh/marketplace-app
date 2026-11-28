<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListWorkQueueRequest extends FormRequest
{
    public const PENDING_PROVIDERS = 'pending_providers';

    public const UPCOMING_PENDING_BOOKINGS = 'upcoming_pending_bookings';

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'required', 'string', Rule::in([
                self::PENDING_PROVIDERS,
                self::UPCOMING_PENDING_BOOKINGS,
            ])],
        ];
    }
}
