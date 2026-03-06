<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the templates for the category.
     */
    public function templates()
    {
        return $this->hasMany(Template::class , 'category_id');
    }
}
