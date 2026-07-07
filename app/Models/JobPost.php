<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'job_type',
        'category',
        'experience_level',
        'salary_min',
        'salary_max',
        'salary_currency',
        'company_name',
        'company_description',
        'company_logo',
        'contact_email',
        'contact_phone',
        'status',
        'published_at',
        'expires_at',
        'views_count',
    ];

    protected $casts = [
        'published_at'   => 'datetime',
        'expires_at'     => 'datetime',
        'salary_min'     => 'integer',
        'salary_max'     => 'integer',
        'views_count'    => 'integer',
        'is_remote'      => 'boolean',
        'is_hot'         => 'boolean',
    ];

    // ─── Config maps ─────────────────────────────────────────────────────────
    const JOB_TYPES = [
        'full-time'  => ['label' => 'Toàn thời gian',  'color' => 'bg-indigo-50 text-indigo-700',   'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        'part-time'  => ['label' => 'Bán thời gian',    'color' => 'bg-amber-50 text-amber-700',     'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        'contract'   => ['label' => 'Hợp đồng',         'color' => 'bg-teal-50 text-teal-700',      'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'internship' => ['label' => 'Thực tập',         'color' => 'bg-emerald-50 text-emerald-700','icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        'remote'     => ['label' => 'Remote / Từ xa',   'color' => 'bg-violet-50 text-violet-700',  'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];

    const EXPERIENCE_LEVELS = [
        'fresher'   => ['label' => 'Fresher (0-1 năm)', 'short' => 'Fresher'],
        'junior'    => ['label' => 'Junior (1-3 năm)',  'short' => 'Junior'],
        'middle'    => ['label' => 'Middle (3-5 năm)',  'short' => 'Middle'],
        'senior'    => ['label' => 'Senior (5+ năm)',   'short' => 'Senior'],
        'lead'      => ['label' => 'Lead / Manager',      'short' => 'Lead'],
    ];

    const CATEGORIES = [
        'it'         => ['label' => 'Công nghệ thông tin', 'color' => 'indigo'],
        'marketing'  => ['label' => 'Marketing',            'color' => 'rose'],
        'design'     => ['label' => 'Thiết kế',            'color' => 'violet'],
        'finance'    => ['label' => 'Tài chính / Kế toán', 'color' => 'emerald'],
        'hr'         => ['label' => 'Nhân sự',             'color' => 'amber'],
        'sales'      => ['label' => 'Kinh doanh / Bán hàng','color' => 'sky'],
        'operation'  => ['label' => 'Vận hành / QA',       'color' => 'teal'],
        'consulting' => ['label' => 'Tư vấn',              'color' => 'fuchsia'],
        'education'  => ['label' => 'Giáo dục / Đào tạo', 'color' => 'orange'],
        'other'      => ['label' => 'Khác',                'color' => 'gray'],
    ];

    const SORT_OPTIONS = [
        'newest'     => 'Mới nhất',
        'oldest'     => 'Cũ nhất',
        'salary_high'=> 'Lương cao → thấp',
        'salary_low' => 'Lương thấp → cao',
        'title_asc'  => 'Theo tên A → Z',
    ];

    const SALARY_RANGES = [
        0          => 'Tất cả mức lương',
        5000000    => 'Từ 5 triệu',
        10000000   => 'Từ 10 triệu',
        15000000   => 'Từ 15 triệu',
        20000000   => 'Từ 20 triệu',
        30000000   => 'Từ 30 triệu',
        50000000   => 'Từ 50 triệu',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeHot($query)
    {
        return $query->where('is_hot', true);
    }

    public function scopeRemote($query)
    {
        return $query->where('is_remote', true);
    }

    public function scopeInCategory($query, string $cat)
    {
        return $query->where('category', $cat);
    }

    public function scopeOfType($query, string|array $types)
    {
        return $query->whereIn('job_type', (array) $types);
    }

    public function scopeAtExperience($query, string|array $levels)
    {
        return $query->whereIn('experience_level', (array) $levels);
    }

    public function scopeSalaryRange($query, ?int $min, ?int $max)
    {
        if ($min) $query->where('salary_max', '>=', $min);
        if ($max) $query->where('salary_min', '<=', $max);
        return $query;
    }

    public function scopeSearch($query, string $term)
    {
        $t = mb_strtolower(trim($term));
        return $query->where(function ($q) use ($t) {
            $q->whereRaw('LOWER(title) LIKE ?', ["%{$t}%"])
              ->orWhereRaw('LOWER(company_name) LIKE ?', ["%{$t}%"])
              ->orWhereRaw('LOWER(location) LIKE ?', ["%{$t}%"])
              ->orWhereRaw('LOWER(description) LIKE ?', ["%{$t}%"]);
        });
    }

    // ─── Accessors ─────────────────────────────────────────────────────────

    public function getTypeInfoAttribute(): array
    {
        return self::JOB_TYPES[$this->job_type] ?? ['label' => $this->job_type ?? '', 'color' => 'bg-gray-50 text-gray-600', 'icon' => ''];
    }

    public function getExperienceInfoAttribute(): array
    {
        return self::EXPERIENCE_LEVELS[$this->experience_level] ?? ['label' => $this->experience_level ?? '', 'short' => ''];
    }

    public function getCategoryInfoAttribute(): array
    {
        return self::CATEGORIES[$this->category] ?? ['label' => $this->category ?? '', 'color' => 'gray'];
    }

    public function getSalaryLabelAttribute(): string
    {
        if (!$this->salary_min && !$this->salary_max) return 'Thương lượng';
        if ($this->salary_min && $this->salary_max) {
            return number_format($this->salary_min / 1_000_000, 0) . ' - ' . number_format($this->salary_max / 1_000_000, 0) . ' triệu';
        }
        if ($this->salary_min) return 'Từ ' . number_format($this->salary_min / 1_000_000, 0) . ' triệu';
        return 'Đến ' . number_format($this->salary_max / 1_000_000, 0) . ' triệu';
    }

    public function getSalaryRangeAttribute(): array
    {
        return [
            'min' => $this->salary_min ? number_format($this->salary_min / 1_000_000, 0) . ' triệu' : null,
            'max' => $this->salary_max ? number_format($this->salary_max / 1_000_000, 0) . ' triệu' : null,
        ];
    }

    public function getIsNewAttribute(): bool
    {
        return $this->published_at && $this->published_at->diffInDays(now()) <= 3;
    }

    public function getIsUrgentAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->diffInDays(now()) <= 3;
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        return $this->expires_at?->diffInDays(now(), false);
    }

    public function getPublishedForHumansAttribute(): string
    {
        return $this->published_at?->diffForHumans() ?? '';
    }

    public function getCompanyInitialsAttribute(): string
    {
        $name = $this->company_name ?: $this->title ?: 'J';
        $words = preg_split('/\s+/', $name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    public function getCompanyLogoUrlAttribute(): ?string
    {
        if (!$this->company_logo) return null;
        return str_starts_with($this->company_logo, 'http')
            ? $this->company_logo
            : asset('storage/' . $this->company_logo);
    }

    public function getViewsLabelAttribute(): string
    {
        $n = $this->views_count ?? 0;
        if ($n >= 1000) return number_format($n / 1000, 1) . 'K';
        return (string) $n;
    }

    public function getShareUrlAttribute(): string
    {
        return route('jobs.show', $this);
    }

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    // ─── Status helpers ─────────────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }
}
