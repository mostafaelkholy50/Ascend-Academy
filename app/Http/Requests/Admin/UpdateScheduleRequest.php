<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'teacher_id' => 'required|exists:users,id',
            'starts_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'zoom_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:scheduled,completed,cancelled',
        ];
    }
}
