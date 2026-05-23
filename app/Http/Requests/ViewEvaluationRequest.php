<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ViewEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2020|max:' . (now()->year + 1),
        ];
    }

    public function messages(): array
    {
        return [
            'month.between' => 'Month must be between 1 and 12.',
            'year.min' => 'Year cannot be before 2020.',
            'year.max' => 'Year cannot be in the far future.',
        ];
    }
}
