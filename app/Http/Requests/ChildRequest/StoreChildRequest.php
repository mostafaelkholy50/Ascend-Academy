<?php

namespace App\Http\Requests\ChildRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChildRequest extends FormRequest
{
       public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // بيانات الطالب
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],

            // معلومات إضافية
            'level' => ['nullable', 'string', 'max:100'],
            'course_id' => ['nullable', 'exists:courses,id'],
        ];
    }
}
