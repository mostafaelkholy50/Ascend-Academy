<?php

namespace App\Http\Requests\EnrollmentRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
   public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'status' => ['nullable', Rule::in(['active', 'completed', 'cancelled'])],
            
            // Flexible Schedule & Pricing
            'days_per_week' => ['required', 'integer', 'min:1', 'max:7'],
            'session_duration' => ['required', 'integer', 'in:30,45,60,90,120'],
            'admin_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'in:USD,CAD,GBP,EUR'],
        ];
    }
}
