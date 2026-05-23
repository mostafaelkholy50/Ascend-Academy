<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date|before:today',
            'country' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:users,id',
        ];
    }
}
