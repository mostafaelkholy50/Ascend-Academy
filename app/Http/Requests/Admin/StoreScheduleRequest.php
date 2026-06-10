<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Assuming authorization is handled by middleware
    }

    public function rules()
    {
        return [
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:users,id',
            'admin_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|in:CAD,USD,GBP,EUR',
            'days' => 'required|array|min:1',
            'days.*' => 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'schedule_times' => 'required|array|min:1',
            'schedule_times.*' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'zoom_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
            'month' => 'nullable|date_format:Y-m',
            'start_date' => 'required|date',
        ];
    }
}
