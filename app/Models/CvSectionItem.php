<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvSectionItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cv_section_id',
        'content',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'content' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Get the section that owns the item.
     */
    public function section()
    {
        return $this->belongsTo(CvSection::class , 'cv_section_id');
    }
}
