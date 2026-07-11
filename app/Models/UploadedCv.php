<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadedCv extends Model
{
    protected $table = 'uploaded_cvs';

    protected $fillable = [
        'user_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'extracted_text',
        'extracted_skills',
        'experience_level',
        'parsed_at',
    ];

    protected $casts = [
        'extracted_skills' => 'array',
        'parsed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}