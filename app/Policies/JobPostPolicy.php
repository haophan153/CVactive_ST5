<?php

namespace App\Policies;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class JobPostPolicy
{
    /**
     * Chỉ chủ sở hữu HOẶC admin mới được xem chi tiết job post
     */
    public function view(User $user, JobPost $jobPost): bool
    {
        // Job post công khai ai cũng xem được
        if ($jobPost->isPublished()) {
            return true;
        }

        // Chỉ chủ sở hữu hoặc admin mới xem được job post không công khai
        return $user->role === 'admin' || $jobPost->user_id === $user->id;
    }

    /**
     * Chỉ chủ sở hữu HOẶC admin mới được chỉnh sửa
     */
    public function update(User $user, JobPost $jobPost): bool
    {
        return $user->role === 'admin' || $jobPost->user_id === $user->id;
    }

    /**
     * Chỉ chủ sở hữu HOẶC admin mới được xóa
     */
    public function delete(User $user, JobPost $jobPost): bool
    {
        return $user->role === 'admin' || $jobPost->user_id === $user->id;
    }

    /**
     * Chỉ chủ sở hữu HOẶC admin mới được publish
     */
    public function publish(User $user, JobPost $jobPost): bool
    {
        return $user->role === 'admin' || $jobPost->user_id === $user->id;
    }

    /**
     * Chỉ chủ sở hữu HOẶC admin mới được close job post
     */
    public function close(User $user, JobPost $jobPost): bool
    {
        return $user->role === 'admin' || $jobPost->user_id === $user->id;
    }

    /**
     * Chỉ HR hoặc admin mới được tạo job post
     */
    public function create(User $user): bool
    {
        return $user->isHR() || $user->role === 'admin';
    }

    /**
     * Chỉ chủ sở hữu HOẶC admin mới được xem ứng viên
     */
    public function viewApplications(User $user, JobPost $jobPost): bool
    {
        $isAuthorized = $user->role === 'admin' || $jobPost->user_id === $user->id;

        Log::channel('cv_access')->info('Job Post Applications View Attempt', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'job_post_id' => $jobPost->id,
            'job_post_owner_id' => $jobPost->user_id,
            'authorized' => $isAuthorized,
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return $isAuthorized;
    }

    /**
     * Chỉ chủ sở hữu HOẶC admin mới được tìm kiếm CV trong job post
     */
    public function searchCv(User $user, JobPost $jobPost): bool
    {
        return $user->role === 'admin' || $jobPost->user_id === $user->id;
    }
}
