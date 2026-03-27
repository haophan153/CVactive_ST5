<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ApplicationPolicy
{
    /**
     * Chỉ HR sở hữu job post HOẶC admin mới được xem danh sách ứng viên
     */
    public function viewApplications(User $user, JobPost $jobPost): bool
    {
        $isAuthorized = $user->role === 'admin' || $jobPost->user_id === $user->id;

        Log::channel('cv_access')->info('CV Applications View Attempt', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'job_post_id' => $jobPost->id,
            'job_post_owner_id' => $jobPost->user_id,
            'authorized' => $isAuthorized,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return $isAuthorized;
    }

    /**
     * Chỉ HR sở hữu job post HOẶC admin mới được xem chi tiết ứng viên
     */
    public function view(User $user, JobApplication $application): bool
    {
        $isAuthorized = $user->role === 'admin' || $application->isOwnedBy($user);

        Log::channel('cv_access')->info('CV Application View Attempt', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'application_id' => $application->id,
            'job_post_id' => $application->job_post_id,
            'job_post_owner_id' => $application->jobPost?->user_id,
            'authorized' => $isAuthorized,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return $isAuthorized;
    }

    /**
     * CHỈ HR sở hữu job post HOẶC admin mới được tải CV file
     * ĐÂY LÀ METHOD BẢO MẬT QUAN TRỌNG NHẤT
     */
    public function downloadCv(User $user, JobApplication $application): bool
    {
        $isAuthorized = $user->role === 'admin' || $application->isOwnedBy($user);

        Log::channel('cv_access')->info('CV Download Attempt', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'application_id' => $application->id,
            'candidate_name' => $application->full_name,
            'candidate_email' => $application->email,
            'job_post_id' => $application->job_post_id,
            'job_post_owner_id' => $application->jobPost?->user_id,
            'cv_path' => $application->cv_path ?? $application->cv_file,
            'authorized' => $isAuthorized,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return $isAuthorized;
    }

    /**
     * HR sở hữu job post HOẶC admin mới được cập nhật trạng thái
     */
    public function updateStatus(User $user, JobApplication $application): bool
    {
        return $user->role === 'admin' || $application->isOwnedBy($user);
    }

    /**
     * HR sở hữu job post HOẶC admin mới được xóa đơn
     */
    public function delete(User $user, JobApplication $application): bool
    {
        return $user->role === 'admin' || $application->isOwnedBy($user);
    }

    /**
     * HR sở hữu job post HOẶC admin mới được tìm kiếm CV trong tin tuyển dụng
     */
    public function searchCv(User $user, JobPost $jobPost): bool
    {
        return $user->role === 'admin' || $jobPost->user_id === $user->id;
    }

    /**
     * HR sở hữu job post HOẶC admin mới được xem dashboard ứng viên
     */
    public function viewDashboard(User $user, JobPost $jobPost): bool
    {
        return $user->role === 'admin' || $jobPost->user_id === $user->id;
    }
}
