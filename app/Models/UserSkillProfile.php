<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkillProfile extends Model
{
    protected $fillable = [
        'user_id',
        'cv_id',
        'skills',
        'job_titles',
        'companies',
        'experience_level',
        'preferred_categories',
        'preferred_job_types',
        'salary_expectation_min',
        'salary_expectation_max',
        'last_extracted_at',
    ];

    protected $casts = [
        'skills' => 'array',
        'job_titles' => 'array',
        'companies' => 'array',
        'preferred_categories' => 'array',
        'preferred_job_types' => 'array',
        'salary_expectation_min' => 'integer',
        'salary_expectation_max' => 'integer',
        'last_extracted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function skillsText(): string
    {
        return implode(', ', $this->skills ?? []);
    }

    public function isStale(): bool
    {
        if (!$this->last_extracted_at) {
            return true;
        }
        return $this->last_extracted_at->diffInDays(now()) >= 7;
    }
}
