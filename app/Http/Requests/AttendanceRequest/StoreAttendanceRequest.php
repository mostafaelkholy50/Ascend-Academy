<?php

namespace App\Http\Requests\AttendanceRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedule_id' => ['required', 'exists:schedules,id'],
            'student_id' => ['required', 'exists:users,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'student_present' => ['required', 'boolean'],
            'teacher_present' => ['required', 'boolean'],
            'remark' => [
                'nullable',
                'string',
                'max:1000',
                // Remark إلزامي إذا كان هناك غياب
                function ($attribute, $value, $fail) {
                    if (!$this->student_present || !$this->teacher_present) {
                        if (empty($value)) {
                            $fail('Remark is required when there is an absence.');
                        }
                    }
                },
            ],
        ];
    }
}
