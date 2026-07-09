<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvSection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cv_id',
        'type',
        'title',
        'sort_order',
        'is_visible',
        'is_custom',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_visible' => 'boolean',
        'is_custom' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * L1: Auto-normalize sort_order trước khi save.
     *
     * Nếu 2 user cùng tạo section với sort_order = 5, sẽ có duplicate.
     * Logic này shift các section >= $sort_order của cùng CV lên 1,
     * đảm bảo sort_order là duy nhất trong scope 1 CV.
     *
     * Cũng validate: nếu sort_order < 0 → set = 0.
     */
    protected static function booted(): void
    {
        static::creating(function (self $section) {
            // L1: Nếu không truyền sort_order, append cuối
            if ($section->sort_order === null) {
                $max = static::where('cv_id', $section->cv_id)->max('sort_order') ?? -1;
                $section->sort_order = $max + 1;
                return;
            }

            $order = max(0, (int) $section->sort_order);

            // L1: shift các section có sort_order >= $order lên 1
            // để tránh duplicate trong cùng CV.
            static::where('cv_id', $section->cv_id)
                ->where('sort_order', '>=', $order)
                ->increment('sort_order');

            $section->sort_order = $order;
        });

        static::updating(function (self $section) {
            // Khi user kéo-thả đổi sort_order của section hiện tại
            if ($section->isDirty('sort_order')) {
                $newOrder = max(0, (int) $section->sort_order);
                $oldOrder = (int) $section->getOriginal('sort_order');

                if ($newOrder === $oldOrder) {
                    return;
                }

                if ($newOrder > $oldOrder) {
                    // Kéo xuống: shift các section ở giữa (oldOrder, newOrder] lên
                    static::where('cv_id', $section->cv_id)
                        ->where('id', '!=', $section->id)
                        ->whereBetween('sort_order', [$oldOrder + 1, $newOrder])
                        ->decrement('sort_order');
                } else {
                    // Kéo lên: shift các section ở giữa [newOrder, oldOrder) xuống
                    static::where('cv_id', $section->cv_id)
                        ->where('id', '!=', $section->id)
                        ->whereBetween('sort_order', [$newOrder, $oldOrder - 1])
                        ->increment('sort_order');
                }
            }
        });
    }

    /**
     * Get the CV that owns the section.
     */
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }

    /**
     * Get the items for the section.
     */
    public function items()
    {
        return $this->hasMany(CvSectionItem::class)->orderBy('sort_order', 'asc');
    }
}