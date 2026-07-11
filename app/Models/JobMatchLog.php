<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobMatchLog extends Model
{
    protected $fillable = [
        'user_id',
        'job_post_id',
        'rule_score',
        'ai_score',
        'matched_skills',
        'missing_skills',
        'sent_at',
        'viewed_at',
        'applied_at',
    ];

    protected $casts = [
        'rule_score' => 'integer',
        'ai_score' => 'integer',
        'matched_skills' => 'array',
        'missing_skills' => 'array',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'applied_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function getFinalScoreAttribute(): int
    {
        return $this->ai_score ?? $this->rule_score;
    }

    public function getScoreLabelAttribute(): string
    {
        $score = $this->final_score;
        if ($score >= 80) return 'Rất phù hợp';
        if ($score >= 60) return 'Phù hợp';
        if ($score >= 40) return 'Khá phù hợp';
        return 'Ít phù hợp';
    }

    public function wasRecentlySent(): bool
    {
        if (!$this->sent_at) {
            return false;
        }
        return $this->sent_at->diffInDays(now()) < 7;
    }
}
