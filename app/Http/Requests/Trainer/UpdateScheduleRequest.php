<?php

namespace App\Http\Requests\Trainer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateScheduleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['sometimes', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'recurrence_mode' => [
                'sometimes',
                Rule::in(['one_off', 'daily', 'weekly', 'monthly', 'weekdays']),
            ],
            'recurrence_interval' => ['nullable', 'integer', 'min:1', 'max:365'],
            'recurrence_weekdays' => ['nullable', 'array'],
            'recurrence_weekdays.*' => ['integer', 'min:0', 'max:6'],
            'recurrence_ends_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
