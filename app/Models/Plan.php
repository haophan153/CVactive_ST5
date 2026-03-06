<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'price',
        'cv_limit',
        'features',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get the users that subscribed to this plan.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the payments associated with this plan.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
