<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherApplicationStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => 'required|in:pending,reviewed,approved,rejected,converted',
            'admin_notes' => 'nullable|string|max:2000'
        ];
    }
}
