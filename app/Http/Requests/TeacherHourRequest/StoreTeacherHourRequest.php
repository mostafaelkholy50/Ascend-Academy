<?php

namespace App\Http\Requests\TeacherHourRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherHourRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'exists:users,id'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'total_hours' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'hourly_rate' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_paid' => ['nullable', 'boolean'],
            'paid_at' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }
}
