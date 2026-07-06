<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'job_type' => ['nullable', 'string', 'in:full-time,part-time,contract,internship,remote'],
            'category' => ['nullable', 'string', 'max:50'],
            'experience_level' => ['nullable', 'string', 'in:fresher,junior,middle,senior,lead'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['nullable', 'string', 'max:10'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_description' => ['nullable', 'string'],
            'company_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:draft,published,closed'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'is_remote' => ['nullable', 'boolean'],
            'is_hot' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề tin tuyển dụng là bắt buộc.',
            'description.required' => 'Mô tả công việc là bắt buộc.',
            'salary_max.gte' => 'Lương tối đa phải lớn hơn hoặc bằng lương tối thiểu.',
        ];
    }
}
