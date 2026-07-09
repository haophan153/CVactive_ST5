<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'author_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'views_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'views_count'  => 'integer',
        'is_featured' => 'boolean',
    ];

    /**
     * L2: Auto-fill excerpt + slug từ content/title khi không có.
     */
    protected static function booted(): void
    {
        static::saving(function (self $post) {
            // Auto-slug từ title khi chưa có
            if (!$post->slug && $post->title) {
                $post->slug = \Illuminate\Support\Str::slug($post->title);
            }

            // L2: Auto-generate excerpt từ content khi null/rỗng
            if (empty($post->excerpt) && !empty($post->content)) {
                $plain = trim(strip_tags($post->content));
                // Lấy 200 ký tự đầu (an toàn UTF-8 cho tiếng Việt)
                $post->excerpt = mb_substr($plain, 0, 200);
                if (mb_strlen($plain) > 200) {
                    $post->excerpt .= '…';
                }
            }
        });
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfCategory($query, string $slug)
    {
        return $query->whereHas('category', fn($q) => $q->where('slug', $slug));
    }

    public function scopeSearch($query, string $term)
    {
        $t = mb_strtolower(trim($term));
        return $query->where(function ($q) use ($t) {
            $q->whereRaw('LOWER(title) LIKE ?', ["%{$t}%"])
              ->orWhereRaw('LOWER(excerpt) LIKE ?', ["%{$t}%"])
              ->orWhereRaw('LOWER(content) LIKE ?', ["%{$t}%"]);
        });
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getExcerptTruncatedAttribute(): string
    {
        return \Illuminate\Support\Str::limit($this->excerpt ?? '', 120);
    }

    public function getReadingTimeLabelAttribute(): string
    {
        $minutes = $this->reading_time ?? 5;
        return $minutes . ' phút đọc';
    }

    public function getExcerptAttribute($value): ?string
    {
        return $value ?: null;
    }

    /**
     * Get the author of the post.
     */
    public function author()
    {
        return $this->belongsTo(User::class , 'author_id');
    }

    /**
     * Get the category of the post.
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class , 'category_id');
    }
}
