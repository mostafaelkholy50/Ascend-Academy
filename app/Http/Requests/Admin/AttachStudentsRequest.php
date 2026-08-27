<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachStudentsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'Student')->where('active', true);
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'student_ids.required' => 'Please select at least one student.',
            'student_ids.array' => 'Selected students must be a valid list.',
            'student_ids.min' => 'Please select at least one student.',
            'student_ids.*.exists' => 'One or more selected students are invalid or inactive.',
        ];
    }
}
