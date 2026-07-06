<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    public const CATEGORIES = [
        'general'     => 'Tổng quan',
        'account'     => 'Tài khoản',
        'cv'          => 'Tạo CV',
        'payment'     => 'Thanh toán',
        'job'         => 'Việc làm',
        'security'    => 'Bảo mật',
    ];

    protected $fillable = [
        'question',
        'answer',
        'category',
        'sort_order',
        'is_active',
        'views_count',
    ];

    protected $casts = [
        'sort_order'  => 'integer',
        'is_active'   => 'boolean',
        'views_count' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Khác';
    }

    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'account'  => 'user',
            'cv'       => 'document',
            'payment'  => 'credit-card',
            'job'      => 'briefcase',
            'security' => 'shield',
            default    => 'sparkles',
        };
    }
}