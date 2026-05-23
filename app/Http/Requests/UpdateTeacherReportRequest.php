<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Report;

class UpdateTeacherReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $report = $this->route('report');
        return auth()->check() && auth()->user()->role === 'Teacher' && $report && $report->teacher_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'level' => 'nullable|string|max:100',
            'mastery_score' => 'nullable|integer|min:0|max:100',
            'strengths' => 'nullable|string|max:1000',
            'weaknesses' => 'nullable|string|max:1000',
            'behavior' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'report_date' => 'required|date|before_or_equal:today',
        ];
    }
}
