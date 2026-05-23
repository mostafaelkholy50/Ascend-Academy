<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $studentId = $this->route('student')->id ?? $this->route('student');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $studentId,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date|before:today',
            'country' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
        ];
    }
}
