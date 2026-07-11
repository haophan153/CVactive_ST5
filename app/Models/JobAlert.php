<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAlert extends Model
{
    protected $table = 'user_job_alerts';

    protected $fillable = [
        'user_id',
        'is_active',
        'match_threshold',
        'notification_frequency',
        'preferred_categories',
        'preferred_job_types',
        'preferred_locations',
        'notify_new_jobs',
        'notify_salary_up',
        'last_sent_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'match_threshold' => 'integer',
        'preferred_categories' => 'array',
        'preferred_job_types' => 'array',
        'preferred_locations' => 'array',
        'notify_new_jobs' => 'boolean',
        'notify_salary_up' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skillProfile(): BelongsTo
    {
        return $this->belongsTo(UserSkillProfile::class, 'user_id', 'user_id');
    }

    public function canSendDaily(): bool
    {
        if (!$this->is_active || $this->notification_frequency !== 'daily') {
            return false;
        }
        if (!$this->last_sent_at) {
            return true;
        }
        return $this->last_sent_at->startOfDay()->lt(now()->startOfDay());
    }

    public function markSent(): void
    {
        $this->update(['last_sent_at' => now()]);
    }
}
