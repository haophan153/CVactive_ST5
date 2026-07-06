<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ApplyJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_post_id' => ['required', 'integer', 'exists:job_posts,id'],
            'cv_id' => ['nullable', 'integer', 'exists:cvs,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'cover_letter' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'job_post_id.required' => 'Vui lòng chọn tin tuyển dụng.',
            'job_post_id.exists' => 'Tin tuyển dụng không tồn tại.',
            'full_name.required' => 'Họ tên là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'cv_file.mimes' => 'CV phải là file PDF, DOC hoặc DOCX.',
            'cv_file.max' => 'Kích thước file CV không được vượt quá 5MB.',
        ];
    }
}
