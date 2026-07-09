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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'view_count' => 'integer',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the CV being shared.
     */
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
