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
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

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
