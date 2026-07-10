<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'template_id' => ['sometimes', 'required', 'integer', 'exists:templates,id'],
            'theme_color' => ['sometimes', 'nullable', 'string', 'max:20'],
            'font_family' => ['sometimes', 'nullable', 'string', 'max:50'],
            'visibility' => ['sometimes', Rule::in(['public', 'private'])],
            'objective' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'is_draft' => ['sometimes', 'boolean'],
            'personal_info' => ['sometimes', 'nullable', 'array'],
            'personal_info.full_name' => ['sometimes', 'string', 'max:200'],
            'personal_info.email' => ['sometimes', 'email', 'max:255'],
            'personal_info.phone' => ['sometimes', 'string', 'max:30', 'regex:/^[0-9+\-\s()]*$/'],
            'personal_info.address' => ['sometimes', 'string', 'max:500'],
            'personal_info.website' => ['sometimes', 'nullable', 'url', 'max:500'],
            'personal_info.linkedin' => ['sometimes', 'nullable', 'url', 'max:500', 'regex:#^https?://(www\.)?linkedin\.com/#i'],
            'personal_info.github' => ['sometimes', 'nullable', 'url', 'max:500', 'regex:#^https?://(www\.)?github\.com/#i'],
        ];
    }

    /**
     * SECURITY (fix #12): Strip dangerous tags / protocols from any HTML-bearing
     * fields. The `objective` and free-text personal_info fields are rendered
     * directly in the public CV view, so a stored XSS through them would
     * compromise every visitor of the share link.
     */
    protected function passedValidation(): void
    {
        $allowedTags = '<b><strong><i><em><u><br><p><ul><ol><li>';

        $objective = $this->input('objective');
        if (is_string($objective)) {
            $this->merge([
                'objective' => strip_tags($objective, $allowedTags),
            ]);
        }

        $personal = $this->input('personal_info');
        if (is_array($personal)) {
            foreach (['address'] as $k) {
                if (isset($personal[$k]) && is_string($personal[$k])) {
                    $personal[$k] = strip_tags($personal[$k], $allowedTags);
                }
            }
            $this->merge(['personal_info' => $personal]);
        }
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề CV là bắt buộc.',
            'template_id.exists' => 'Template không hợp lệ.',
            'visibility.in' => 'Quyền riêng tư không hợp lệ.',
            'personal_info.email.email' => 'Email không hợp lệ.',
            'personal_info.phone.regex' => 'Số điện thoại chỉ chứa chữ số và + - ( ).',
            'personal_info.website.url' => 'Website phải là URL hợp lệ.',
            'personal_info.linkedin.regex' => 'LinkedIn phải bắt đầu bằng https://linkedin.com/',
            'personal_info.github.regex' => 'GitHub phải bắt đầu bằng https://github.com/',
        ];
    }
}
