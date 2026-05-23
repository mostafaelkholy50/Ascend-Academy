<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // Assuming any authenticated user with access can store (handled by middleware or controller logic)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q1_score' => 'required|integer|min:0|max:10',
            'q2_score' => 'required|integer|min:0|max:10',
            'q3_score' => 'required|integer|min:0|max:10',
            'q4_score' => 'required|integer|min:0|max:10',
            'q5_score' => 'required|integer|min:0|max:10',
            'q6_score' => 'required|integer|min:0|max:10',
            'q7_score' => 'required|integer|min:0|max:10',
            'q8_score' => 'required|integer|min:0|max:10',
            'q9_score' => 'required|integer|min:0|max:10',
            'q10_score' => 'required|integer|min:0|max:10',
            'notes' => 'nullable|string',
        ];
    }
}
