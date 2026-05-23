<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'student_id' => 'required|exists:users,id',
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:courses,id',
            'status' => 'nullable|in:active,completed,cancelled',
            'days_per_week' => 'required|integer|min:1|max:7',
            'session_duration' => 'required|in:30,60',
            'admin_price' => 'required|numeric|min:0',
            'currency' => 'required|in:CAD,USD,GBP',
        ];
    }
}
