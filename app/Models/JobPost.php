<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'job_type',
        'salary_min',
        'salary_max',
        'salary_currency',
        'company_name',
        'company_description',
        'company_logo',
        'contact_email',
        'contact_phone',
        'status',
        'published_at',
        'expires_at',
        'views_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'salary_min' => 'integer',
        'salary_max' => 'integer',
        'views_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function close()
    {
        $this->update([
            'status' => 'closed',
        ]);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}
