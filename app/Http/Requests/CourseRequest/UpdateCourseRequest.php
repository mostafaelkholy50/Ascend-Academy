<?php

namespace App\Http\Requests\CourseRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'duration_weeks' => ['nullable', 'integer', 'min:1', 'max:52'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }
}
