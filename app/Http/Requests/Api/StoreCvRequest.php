<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'template_id' => ['nullable', 'integer', 'exists:templates,id'],
            'personal_info' => ['nullable', 'array'],
            'personal_info.name' => ['nullable', 'string', 'max:255'],
            'personal_info.email' => ['nullable', 'email', 'max:255'],
            'personal_info.phone' => ['nullable', 'string', 'max:50'],
            'personal_info.address' => ['nullable', 'string', 'max:500'],
            'personal_info.summary' => ['nullable', 'string', 'max:2000'],
            'objective' => ['nullable', 'string', 'max:2000'],
            'theme_color' => ['nullable', 'string', 'max:20'],
            'font_family' => ['nullable', 'string', 'max:100'],
            'visibility' => ['nullable', 'in:public,private,link'],
            'is_draft' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề CV là bắt buộc.',
            'template_id.exists' => 'Template không tồn tại.',
        ];
    }
}
