<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $application = $this->route('application');
        return $application && ($this->user()?->role === 'admin' || $application->jobPost?->user_id === $this->user()->id);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending,reviewing,approved,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
