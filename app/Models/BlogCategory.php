<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the posts for the category.
     */
    public function posts()
    {
        return $this->hasMany(BlogPost::class , 'category_id');
    }
}
