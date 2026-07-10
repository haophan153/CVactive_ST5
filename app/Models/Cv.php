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
     * SECURITY (fix #12): Read accessor that returns personal_info as plaintext-only
     * (no HTML tags). Blade views should always render via {{ $cv->safe_personal_info['full_name'] }}
     * to guarantee escaping. Direct `$cv->personal_info` should be treated as untrusted
     * and rendered with {{ ... }}.
     */
    public function getSafePersonalInfoAttribute(): array
    {
        $info = $this->personal_info ?? [];
        $clean = [];
        foreach ($info as $k => $v) {
            if (is_string($v)) {
                $clean[$k] = strip_tags(trim($v));
            } else {
                $clean[$k] = $v;
            }
        }
        return $clean;
    }

    /**
     * SECURITY (fix #12): Read accessor for objective.
     */
    public function getSafeObjectiveAttribute(): string
    {
        return is_string($this->objective) ? strip_tags(trim($this->objective)) : '';
    }

    /**
     * Get the shares for the CV.
     */
    public function shares()
    {
        return $this->hasMany(CvShare::class);
    }
}
