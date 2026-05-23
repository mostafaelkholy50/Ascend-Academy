<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
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
        $teacher = $this->route('teacher');
        $teacherId = is_object($teacher) ? $teacher->id : $teacher;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacherId,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date|before:today',
            'active' => 'nullable|boolean',
            'country' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
