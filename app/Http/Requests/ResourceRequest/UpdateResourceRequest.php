<?php

namespace App\Http\Requests\ResourceRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:pdf,image,video,audio,link,other',
            'file' => 'nullable|file|max:51200|mimes:pdf,jpg,jpeg,png,mp4,mp3,doc,docx,xls,xlsx,ppt,pptx,txt,zip',
            'external_url' => 'nullable|url|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Please select a student for this resource.',
            'title.required' => 'Please provide a title for the resource.',
            'type.required' => 'Please select a resource type.',
            'type.in' => 'Invalid resource type selected.',
            'file.max' => 'File size must not exceed 50MB.',
            'external_url.url' => 'Please provide a valid URL.',
        ];
    }
}
