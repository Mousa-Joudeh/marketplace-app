<?php

namespace App\Http\Requests\Admin;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ShowAnalyticsRequest extends FormRequest
{
    public const MAX_DAYS = 90;

    public function rules(): array
    {
        return [
            'date_from' => ['required_with:date_to', 'date_format:Y-m-d'],
            'date_to' => ['required_with:date_from', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty() || ! $this->filled(['date_from', 'date_to'])) {
                    return;
                }

                $start = CarbonImmutable::parse($this->input('date_from'), 'UTC');
                $end = CarbonImmutable::parse($this->input('date_to'), 'UTC');

                if ($end->greaterThan($start->addDays(self::MAX_DAYS - 1))) {
                    $validator->errors()->add('date_to', 'The date range may not exceed '.self::MAX_DAYS.' days.');
                }
            },
        ];
    }
}
