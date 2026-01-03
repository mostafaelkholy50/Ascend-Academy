<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Personal Information
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:teacher_applications,email'],
            'phone' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'birth_date' => ['nullable', 'date', 'before:today'],

            // Qualifications
            'education_level' => ['required', 'string', 'max:100'],
            'certifications' => ['nullable', 'string', 'max:2000'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'teaching_experience' => ['required', 'string', 'max:2000'],

            // Teaching Details
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['string'],
            'age_groups' => ['required', 'array', 'min:1'],
            'age_groups.*' => ['string'],
            'teaching_methodology' => ['nullable', 'string', 'max:2000'],
            'availability' => ['required', 'array', 'min:1'],

            // Technical
            'has_stable_internet' => ['required', 'boolean'],
            'has_quiet_space' => ['required', 'boolean'],
            'why_join' => ['required', 'string', 'max:2000'],

            // CV Upload
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.unique' => 'This email has already been used for an application.',
            'subjects.required' => 'Please select at least one subject you can teach.',
            'age_groups.required' => 'Please select at least one age group you can teach.',
            'why_join.required' => 'Please tell us why you want to join our team.',
        ];
    }
}
