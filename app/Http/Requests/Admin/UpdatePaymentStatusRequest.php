<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_status' => 'required|in:paid,unpaid,partial',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
