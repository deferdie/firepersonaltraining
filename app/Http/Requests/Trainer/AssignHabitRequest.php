<?php

namespace App\Http\Requests\Trainer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignHabitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'library_habit_id' => [
                'nullable',
                'exists:library_habits,id',
            ],
            'name' => [
                'required_without:library_habit_id',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }
}
