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
        return $map[$this->color ?? 'indigo'] ?? $map['indigo'];
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
