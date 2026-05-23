<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $parentId = $this->route('parent')->id ?? $this->route('parent');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $parentId,
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
        ];
    }
}
