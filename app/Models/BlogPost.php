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
        'views_count' => 'integer',
    ];

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
