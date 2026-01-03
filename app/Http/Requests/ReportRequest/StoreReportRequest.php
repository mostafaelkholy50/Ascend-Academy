<?php

namespace App\Http\Requests\ReportRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'exists:users,id'],
            'student_id' => ['required', 'exists:users,id'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'level' => ['nullable', 'string', 'max:100'],
            'mastery_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'strengths' => ['nullable', 'string', 'max:1000'],
            'weaknesses' => ['nullable', 'string', 'max:1000'],
            'behavior' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'report_date' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }
}
