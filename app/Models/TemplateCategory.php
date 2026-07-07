<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
    ];

    public function getColorClassesAttribute(): array
    {
        $map = [
            'indigo'  => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'solid' => 'bg-indigo-600'],
            'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'solid' => 'bg-emerald-600'],
            'rose'    => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'solid' => 'bg-rose-600'],
            'amber'   => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'solid' => 'bg-amber-500'],
            'sky'     => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'solid' => 'bg-sky-600'],
            'violet'  => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'solid' => 'bg-violet-600'],
            'slate'   => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'solid' => 'bg-slate-700'],
            'teal'    => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'solid' => 'bg-teal-600'],
        ];
        return $map[$this->color ?? 'indigo'] ?? $map['indigo'];
    }

    public function getIconAttribute($value): string
    {
        return $value ?: 'document-text';
    }

    public function activeTemplates()
    {
        return $this->hasMany(Template::class, 'category_id')->where('is_active', true);
    }
}
