<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AddChildRequest extends FormRequest
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
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'country' => 'nullable|string|max:100',
        ];
    }
}
