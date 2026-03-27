<?php

namespace App\Providers;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Policies\ApplicationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pagination style
        \Illuminate\Pagination\Paginator::useTailwind();

        // ============================================================
        // Register Policies
        // ============================================================
        Gate::policy(JobApplication::class, ApplicationPolicy::class);
        Gate::policy(JobPost::class, \App\Policies\JobPostPolicy::class);

        // ============================================================
        // Define Gates for HR-specific permissions
        // ============================================================
        Gate::define('hr-access', function ($user) {
            return $user->role === 'hr' || $user->role === 'admin';
        });

        Gate::define('admin-access', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('download-application-cv', function ($user, JobApplication $application) {
            // HR chỉ được tải CV của job posts họ sở hữu
            if ($user->role === 'admin') {
                return true;
            }

            if ($user->isHR()) {
                return $application->jobPost?->user_id === $user->id;
            }

            // User thường không được tải CV của người khác
            return false;
        });
    }
}
