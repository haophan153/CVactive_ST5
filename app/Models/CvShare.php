<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvShare extends Model
{
    /**
     * SECURITY (fix #13): Hard upper bound on share lifetime. A token can be
     * created with a shorter expires_at but never longer than 30 days from now.
     * This prevents an attacker (or careless user) from creating a permanent
     * link that lives forever in search engine caches and leak sites.
     */
    public const MAX_LIFETIME_DAYS = 30;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cv_id',
        'share_token',
        'view_count',
        'expires_at',
        'revoked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'view_count' => 'integer',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * Get the CV being shared.
     */
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }

    /**
     * True if the share is currently usable (not revoked and not expired).
     */
    public function isUsable(): bool
    {
        if ($this->revoked_at !== null) {
            return false;
        }
        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }
        return true;
    }

    /**
     * Get a safely clamped expires_at value — never beyond MAX_LIFETIME_DAYS.
     */
    public static function clampExpiration(?\DateTimeInterface $when): ?\Carbon\Carbon
    {
        if ($when === null) {
            return null;
        }

        $max = now()->addDays(self::MAX_LIFETIME_DAYS);

        return min(\Carbon\Carbon::instance(\DateTime::createFromInterface($when)), $max);
    }
}
