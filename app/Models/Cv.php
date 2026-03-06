<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'template_id',
        'title',
        'slug',
        'personal_info',
        'objective',
        'theme_color',
        'font_family',
        'visibility',
        'is_draft',
        'last_saved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'personal_info' => 'array',
        'is_draft' => 'boolean',
        'last_saved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the CV.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template used for the CV.
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the sections for the CV.
     */
    public function sections()
    {
        return $this->hasMany(CvSection::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Get the shares for the CV.
     */
    public function shares()
    {
        return $this->hasMany(CvShare::class);
    }
}
