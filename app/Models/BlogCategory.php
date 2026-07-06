<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
    ];

    public function posts()
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }

    public function publishedPosts()
    {
        return $this->hasMany(BlogPost::class, 'category_id')
            ->where('status', 'published');
    }

    public function getColorClassesAttribute(): array
    {
        $map = [
            'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'ring' => 'ring-indigo-200', 'solid' => 'bg-indigo-600'],
            'rose'   => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'ring' => 'ring-rose-200', 'solid' => 'bg-rose-600'],
            'amber'  => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'ring' => 'ring-amber-200', 'solid' => 'bg-amber-500'],
            'emerald'=> ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-200', 'solid' => 'bg-emerald-600'],
            'sky'    => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'ring' => 'ring-sky-200', 'solid' => 'bg-sky-600'],
            'violet' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'ring' => 'ring-violet-200', 'solid' => 'bg-violet-600'],
            'teal'   => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'ring' => 'ring-teal-200', 'solid' => 'bg-teal-600'],
            'fuchsia'=> ['bg' => 'bg-fuchsia-50', 'text' => 'text-fuchsia-700', 'ring' => 'ring-fuchsia-200', 'solid' => 'bg-fuchsia-600'],
        ];

        return $map[$this->color ?? 'indigo'] ?? $map['indigo'];
    }

    public function getIconAttribute($value): string
    {
        return $value ?: 'document-text';
    }
}
