<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'cv_path',         // Đường dẫn file bảo mật trong storage/private
        'cv_text',
        'cover_letter',
        'status',
        'notes',
        'ai_score',
        'ai_summary',
        'ai_breakdown',
        'ai_scored_at',
        'applied_at',
    ];

    protected $casts = [
        'applied_at'   => 'datetime',
        'ai_scored_at' => 'datetime',
        'ai_breakdown' => 'array',
        'ai_score'     => 'integer',
    ];

    /**
     * Quan hệ với JobPost - mỗi đơn ứng tuyển thuộc về 1 tin tuyển dụng
     */
    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    /**
     * Quan hệ với User (ứng viên)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với Cv của ứng viên
     */
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }

    /**
     * Scope: Lọc đơn đang chờ xử lý
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Lọc đơn theo tin tuyển dụng
     */
    public function scopeByJobPost($query, $jobPostId)
    {
        return $query->where('job_post_id', $jobPostId);
    }

    /**
     * Scope: Lọc đơn đã được AI chấm điểm
     */
    public function scopeScored($query)
    {
        return $query->whereNotNull('ai_score');
    }

    /**
     * Scope: Lọc đơn CHƯA được AI chấm điểm
     */
    public function scopeUnscored($query)
    {
        return $query->whereNull('ai_score');
    }

    /**
     * Accessor: Nhãn điểm AI hiển thị + màu sắc
     * Trả về ['label' => '92/100', 'color' => 'bg-emerald-100 text-emerald-700', 'text' => 'emerald']
     */
    public function getAiScoreLabelAttribute(): array
    {
        if ($this->ai_score === null) {
            return ['label' => null, 'color' => 'bg-gray-100 text-gray-500', 'text' => 'gray'];
        }

        $score = (int) $this->ai_score;

        if ($score >= 70) {
            $color = 'bg-emerald-100 text-emerald-700';
            $text  = 'emerald';
        } elseif ($score >= 40) {
            $color = 'bg-amber-100 text-amber-700';
            $text  = 'amber';
        } else {
            $color = 'bg-red-100 text-red-700';
            $text  = 'red';
        }

        return [
            'label' => $score . '/100',
            'color' => $color,
            'text'  => $text,
        ];
    }

    /**
     * Lấy đường dẫn file CV bảo mật
     * Kiểm tra cả cv_path (private) và cv_file (public) để tương thích ngược
     */
    public function getSecureCvPath(): ?string
    {
        // Ưu tiên cv_path (private storage) nếu có
        if ($this->cv_path) {
            return storage_path('app/private/' . $this->cv_path);
        }

        // Fallback sang cv_file (public storage) - chỉ dùng cho tương thích
        if ($this->cv_file) {
            return storage_path('app/public/' . $this->cv_file);
        }

        return null;
    }

    /**
     * Kiểm tra file CV có tồn tại không
     */
    public function hasCvFile(): bool
    {
        if ($this->cv_path) {
            return Storage::disk('local')->exists($this->cv_path);
        }

        if ($this->cv_file) {
            return Storage::disk('public')->exists($this->cv_file);
        }

        return false;
    }

    /**
     * Lấy tên file CV gốc (nếu có trong metadata)
     */
    public function getOriginalCvFilename(): ?string
    {
        return $this->getAttribute('cv_original_name') ?? 'cv_' . $this->id . '.pdf';
    }

    /**
     * Xóa file CV khi xóa application
     */
    public function deleteCvFile(): void
    {
        if ($this->cv_path) {
            Storage::disk('local')->delete($this->cv_path);
        }

        if ($this->cv_file) {
            Storage::disk('public')->delete($this->cv_file);
        }
    }

    /**
     * Lấy thông tin HR sở hữu tin tuyển dụng
     */
    public function getOwnerAttribute(): ?User
    {
        return $this->jobPost?->user;
    }

    /**
     * Kiểm tra HR hiện tại có phải là chủ sở hữu không
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->jobPost?->user_id === $user->id;
    }

    /**
     * Kiểm tra HR hiện tại có quyền truy cập application không
     * HR chỉ được truy cập CV của tin tuyển dụng mà họ sở hữu
     */
    public function canBeAccessedBy(User $user): bool
    {
        // Admin luôn có quyền
        if ($user->role === 'admin') {
            return true;
        }

        // HR chỉ được truy cập CV thuộc tin tuyển dụng của họ
        if ($user->isHR()) {
            return $this->isOwnedBy($user);
        }

        // User thường chỉ được truy cập đơn của chính mình
        if ($this->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
