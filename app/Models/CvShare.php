<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvShare extends Model
{
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
        'last_viewed_at',
        'revoke_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'view_count'    => 'integer',
        'expires_at'    => 'datetime',
        'revoked_at'    => 'datetime',
        'last_viewed_at' => 'datetime',
    ];

    /**
     * Get the CV being shared.
     */
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }

    /**
     * M-4: Check xem share có còn hợp lệ không (revoked OR expired).
     */
    public function isActive(): bool
    {
        if ($this->revoked_at !== null) {
            return false;
        }
        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }
        return $this->share_token !== null;
    }
}
