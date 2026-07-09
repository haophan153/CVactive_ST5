<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        $cv = $this->route('cv');
        return $cv && $cv->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'template_id' => ['nullable', 'integer', 'exists:templates,id'],
            'personal_info' => ['nullable', 'array'],
            'objective' => ['nullable', 'string', 'max:2000'],
            'theme_color' => ['nullable', 'string', 'max:20'],
            'font_family' => ['nullable', 'string', 'max:100'],
            'visibility' => ['nullable', 'in:public,private,link'],
            'is_draft' => ['nullable', 'boolean'],
        ];
    }
}
