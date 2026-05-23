<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'schedule_id' => 'required|exists:schedules,id',
            'student_present' => 'required|boolean',
            'teacher_present' => 'required|boolean',
            'student_report' => 'required_if:student_present,0|nullable|string',
            'teacher_report' => 'required_if:teacher_present,0|nullable|string',
            'remark' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'student_report.required_if' => 'Please provide a report explaining the student\'s absence.',
            'teacher_report.required_if' => 'Please provide a report explaining the teacher\'s absence.',
        ];
    }
}
