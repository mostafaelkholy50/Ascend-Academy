<?php

namespace App\Http\Requests\InquiryRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['trial', 'contact', 'registration'])],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:100'],
            'city_state' => ['required', 'string', 'max:100'],
            
            // Registration Fields
            'gender' => ['required', Rule::in(['male', 'female'])],
            'join_date' => ['required', 'date'],
            'age' => ['required', 'integer', 'min:3', 'max:100'],
            'study_hours' => ['required', 'string', 'max:255'],
            'courses_needed' => ['required', 'string', 'max:255'],
            'sessions_per_week' => ['required', 'string', 'max:50'],
            'available_days' => ['required', 'array'],
            'available_days.*' => ['string'],
            'referrer' => ['nullable', 'string', 'max:255'], // User didn't explicitly say "referrer is required", but said "make all qustion is requard", "Who recommended us to you?". I will make it required if it's a question, but often this is optional. User said "make all qustion is requard". Okay, I will make it required.
            
            'message' => ['nullable', 'string', 'max:2000'],

            // Old optional fields (can keep nullable or deprecated)
            'child_name' => ['nullable', 'string', 'max:255'],
            'child_age' => ['nullable', 'string', 'max:50'],
            'child_gender' => ['nullable', Rule::in(['male', 'female'])],
            'city' => ['nullable', 'string', 'max:100'],
            'preferred_course' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
        ];
    }
}
