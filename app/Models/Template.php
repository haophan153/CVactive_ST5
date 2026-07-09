<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'blade_view',
        'category_id',
        'is_premium',
        'is_active',
        'theme_color',
        'font_family',
        'usage_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_premium'  => 'boolean',
        'is_active'    => 'boolean',
        'usage_count'  => 'integer',
    ];

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeOfCategory($query, string $slug)
    {
        return $query->whereHas('category', fn($q) => $q->where('slug', $slug));
    }

    public function scopeSearch($query, string $term)
    {
        $t = mb_strtolower(trim($term));
        return $query->whereRaw('LOWER(name) LIKE ?', ["%{$t}%"]);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getUsageLabelAttribute(): string
    {
        $n = $this->usage_count ?? 0;
        if ($n >= 1000) return number_format($n / 1000, 1) . 'K';
        return number_format($n);
    }

    public function getPreviewUrlAttribute(): string
    {
        return route('templates.preview', $this);
    }

    /**
     * Full URL cho thumbnail (hỗ trợ cả relative path lẫn absolute URL).
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $raw = $this->thumbnail;
        if (! $raw) return null;
        if (str_starts_with($raw, 'http')) return $raw;
        return asset('storage/' . ltrim($raw, '/'));
    }

    public function getColorStyleAttribute(): array
    {
        $map = [
            'indigo'  => ['bg' => 'bg-indigo-600',   'text' => 'text-indigo-600',   'hover' => 'hover:bg-indigo-700',   'light' => 'bg-indigo-50',   'border' => 'border-indigo-200'],
            'emerald' => ['bg' => 'bg-emerald-600',  'text' => 'text-emerald-600',  'hover' => 'hover:bg-emerald-700',  'light' => 'bg-emerald-50',  'border' => 'border-emerald-200'],
            'rose'    => ['bg' => 'bg-rose-600',     'text' => 'text-rose-600',     'hover' => 'hover:bg-rose-700',     'light' => 'bg-rose-50',     'border' => 'border-rose-200'],
            'amber'   => ['bg' => 'bg-amber-500',   'text' => 'text-amber-600',    'hover' => 'hover:bg-amber-600',    'light' => 'bg-amber-50',    'border' => 'border-amber-200'],
            'sky'     => ['bg' => 'bg-sky-600',     'text' => 'text-sky-600',      'hover' => 'hover:bg-sky-700',      'light' => 'bg-sky-50',      'border' => 'border-sky-200'],
            'violet'  => ['bg' => 'bg-violet-600',  'text' => 'text-violet-600',   'hover' => 'hover:bg-violet-700',   'light' => 'bg-violet-50',   'border' => 'border-violet-200'],
            'slate'   => ['bg' => 'bg-slate-700',   'text' => 'text-slate-700',    'hover' => 'hover:bg-slate-800',    'light' => 'bg-slate-50',    'border' => 'border-slate-200'],
            'teal'    => ['bg' => 'bg-teal-600',    'text' => 'text-teal-600',     'hover' => 'hover:bg-teal-700',     'light' => 'bg-teal-50',     'border' => 'border-teal-200'],
        ];
        return $map[$this->paletteFromThemeColor($this->theme_color)] ?? $map['indigo'];
    }

    /**
     * L3: Map hex theme_color → palette name gần nhất.
     */
    private function paletteFromThemeColor(?string $hex): string
    {
        if (!$hex || !preg_match('/^#?([0-9a-fA-F]{6})$/', $hex, $m)) {
            return 'indigo';
        }

        $r = hexdec(substr($m[1], 0, 2));
        $g = hexdec(substr($m[1], 2, 2));
        $b = hexdec(substr($m[1], 4, 2));

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $delta = $max - $min;

        if ($delta < 30) {
            return 'slate';
        }

        if ($b > $r && $b > $g) {
            if ($g > 100) return 'sky';
            if ($b > 150 && $r > 100) return 'violet';
            return 'indigo';
        }

        if ($r > $g && $r > $b) {
            if ($g > 100) return 'amber';
            return 'rose';
        }

        if ($g > $r && $g > $b) {
            if ($b > 100) return 'teal';
            return 'emerald';
        }

        return 'indigo';
    }

    /**
     * Get the category that owns the template.
     */
    public function category()
    {
        return $this->belongsTo(TemplateCategory::class , 'category_id');
    }

    /**
     * Get the CVs that use this template.
     */
    public function cvs()
    {
        return $this->hasMany(Cv::class);
    }
}
