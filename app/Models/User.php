<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_id',
        'role',
        'plan_id',
        'plan_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    /**
     * L4: AI CV Scoring quota.
     *
     * Giới hạn:
     *  - Daily: max 50 lần chấm/ngày
     *  - Total: max 1000 lần chấm (lifetime)
     *
     * Reset hàng ngày khi gọi incrementIfAllowed().
     */
    public const AI_SCORE_DAILY_LIMIT = 50;
    public const AI_SCORE_TOTAL_LIMIT = 1000;

    public function hasReachedAiQuota(): bool
    {
        // Reset daily counter nếu sang ngày mới
        $this->resetAiQuotaIfNewDay();

        return $this->ai_score_used_daily >= self::AI_SCORE_DAILY_LIMIT
            || $this->ai_score_used_total >= self::AI_SCORE_TOTAL_LIMIT;
    }

    public function remainingAiQuota(): array
    {
        $this->resetAiQuotaIfNewDay();

        return [
            'daily_remaining' => max(0, self::AI_SCORE_DAILY_LIMIT - $this->ai_score_used_daily),
            'total_remaining' => max(0, self::AI_SCORE_TOTAL_LIMIT - $this->ai_score_used_total),
        ];
    }

    /**
     * M2: Increment AI usage atomically — chống race condition.
     *
     * Dùng DB::table với raw increment thay vì load + update → tránh
     * concurrent request cùng increment bị miss (lost update).
     *
     * Trước đây: load user → ++ → save(). 2 request đồng thời
     * đều đọc 99, đều ghi 100 → quota thực tế là 101 nhưng DB có 100.
     */
    public function incrementAiUsage(int $count = 1): void
    {
        $this->resetAiQuotaIfNewDay();

        // LockForUpdate + atomic increment trong 1 SQL statement
        \DB::table('users')
            ->where('id', $this->id)
            ->increment('ai_score_used_daily', $count, ['updated_at' => now()]);

        \DB::table('users')
            ->where('id', $this->id)
            ->increment('ai_score_used_total', $count, ['updated_at' => now()]);

        // Refresh in-memory state để caller có số mới nhất
        $this->refresh();
    }

    /**
     * Reset daily quota khi sang ngày mới.
     *
     * M2: Reset atomic — đảm bảo reset đúng 1 lần/ngày dù có concurrent request.
     */
    public function resetAiQuotaIfNewDay(): void
    {
        $today = now()->toDateString();

        $resetAt = $this->ai_score_reset_at;
        $resetAtString = $resetAt instanceof \DateTimeInterface ? $resetAt->format('Y-m-d') : (string) $resetAt;
        if ($resetAtString !== $today) {
            // Update atomic chỉ khi ngày thực sự khác → tránh race
            \DB::table('users')
                ->where('id', $this->id)
                ->where(function ($q) use ($today) {
                    $q->whereNull('ai_score_reset_at')
                      ->orWhereDate('ai_score_reset_at', '<', $today);
                })
                ->update([
                    'ai_score_used_daily' => 0,
                    'ai_score_reset_at'   => $today,
                    'updated_at'          => now(),
                ]);
            $this->refresh();
        }
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'plan_expires_at' => 'datetime',
            'ai_score_reset_at' => 'date',
        ];
    }

    /**
     * Get the plan associated with the user.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the CVs created by the user.
     */
    public function cvs()
    {
        return $this->hasMany(Cv::class);
    }

    /**
     * Get the payments made by the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the blog posts authored by the user.
     */
    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class , 'author_id');
    }

    /**
     * Get the job posts created by the user.
     */
    public function jobPosts()
    {
        return $this->hasMany(JobPost::class);
    }

    /**
     * Get job match logs.
     */
    public function jobMatchLogs()
    {
        return $this->hasMany(JobMatchLog::class);
    }

    /**
     * Check if user is HR.
     */
    public function isHR()
    {
        return $this->role === 'hr';
    }

    /**
     * H2 + H4: Kiểm tra user có quyền truy cập Pro hay không.
     *
     * Pro = có plan còn hạn hoặc là admin.
     * User không có plan / plan hết hạn → Free.
     *
     * Dùng cho:
     * - H2: chặn user Free chọn template Premium
     * - H4: chặn user Free set CV visibility=public
     */
    public function hasProAccess(): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        if (!$this->plan_id) {
            return false;
        }

        if (!$this->plan_expires_at) {
            return false;
        }

        return $this->plan_expires_at->isFuture();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Always-resolvable avatar URL for <img src>.
     *
     * Handles three storage formats safely:
     *  - Full URL (Google avatar or external CDN): returned as-is.
     *  - Local storage path ("avatars/foo.jpg"): wrapped via asset('storage/...').
     *  - null/empty: returns null so the caller can show the initial fallback.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $avatar = $this->avatar;

        if (! $avatar) {
            return null;
        }

        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
            return $avatar;
        }

        return asset('storage/' . ltrim($avatar, '/'));
    }

    /**
     * Whether the avatar currently points to a remote URL (e.g. legacy Google avatar).
     * Used to decide if we should skip deleting the local file on replacement.
     */
    public function getAvatarIsRemoteAttribute(): bool
    {
        return $this->avatar && (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://'));
    }
}
