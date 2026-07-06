<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $jobPost = $this->route('jobPost');
        return $jobPost && ($this->user()?->role === 'admin' || $jobPost->user_id === $this->user()->id);
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'job_type' => ['nullable', 'string', 'in:full-time,part-time,contract,internship,remote'],
            'category' => ['nullable', 'string', 'max:50'],
            'experience_level' => ['nullable', 'string', 'in:fresher,junior,middle,senior,lead'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0'],
            'salary_currency' => ['nullable', 'string', 'max:10'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_description' => ['nullable', 'string'],
            'company_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:draft,published,closed'],
            'expires_at' => ['nullable', 'date'],
            'is_remote' => ['nullable', 'boolean'],
            'is_hot' => ['nullable', 'boolean'],
        ];
    }
}
