<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePricingTierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'days_per_week' => 'required|integer|min:1|max:7',
            'session_duration' => 'required|in:30,60',
            'price_cad' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'price_gbp' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
