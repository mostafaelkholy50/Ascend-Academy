<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->isTeacher();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:users,id',
            'q1_score' => 'required|integer|min:1|max:10',
            'q2_score' => 'required|integer|min:1|max:10',
            'q3_score' => 'required|integer|min:1|max:10',
            'q4_score' => 'required|integer|min:1|max:10',
            'q5_score' => 'required|integer|min:1|max:10',
            'q6_score' => 'required|integer|min:1|max:10',
            'q7_score' => 'required|integer|min:1|max:10',
            'q8_score' => 'required|integer|min:1|max:10',
            'q9_score' => 'required|integer|min:1|max:10',
            'q10_score' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string',
        ];
    }
}
