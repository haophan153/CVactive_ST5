<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_post_id',
        'user_id',
        'cv_id',
        'full_name',
        'email',
        'phone',
        'cv_file',
        'cv_text',
        'cover_letter',
        'status',
        'notes',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByJobPost($query, $jobPostId)
    {
        return $query->where('job_post_id', $jobPostId);
    }
}
