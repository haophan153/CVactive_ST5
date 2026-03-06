<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvSection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cv_id',
        'type',
        'title',
        'sort_order',
        'is_visible',
        'is_custom',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_visible' => 'boolean',
        'is_custom' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the CV that owns the section.
     */
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }

    /**
     * Get the items for the section.
     */
    public function items()
    {
        return $this->hasMany(CvSectionItem::class)->orderBy('sort_order', 'asc');
    }
}
