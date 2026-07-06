<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ApplyJobRequest;
use App\Http\Requests\Api\UpdateApplicationStatusRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationController extends ApiController
{
    /**
     * Apply for a job.
     *
     * POST /api/applications
     */
    public function store(ApplyJobRequest $request): JsonResponse
    {
        $jobPost = JobPost::findOrFail($request->job_post_id);

        if ($jobPost->status !== 'published') {
            return $this->error('Tin tuyển dụng này không còn nhận đơn ứng tuyển.', 400);
        }

        $exists = JobApplication::where('job_post_id', $request->job_post_id)
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            return $this->error('Bạn đã ứng tuyển tin này rồi.', 409);
        }

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['applied_at'] = now();
        $data['status'] = 'pending';

        if ($request->hasFile('cv_file')) {
            $file = $request->file('cv_file');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $data['cv_file'] = $file->storeAs('applications', $filename, 'local');
        }

        if ($request->cv_id) {
            $data['cv_id'] = $request->cv_id;
        }

        $application = JobApplication::create($data);

        return $this->success(
            new JobApplicationResource($application->load(['jobPost', 'cv'])),
            'Ứng tuyển thành công!',
            201
        );
    }

    /**
     * List applications for the current user.
     *
     * GET /api/applications
     */
    public function index(Request $request): JsonResponse
    {
        $query = JobApplication::with(['jobPost:id,title,company_name,status'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('applied_at');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(10);

        return $this->paginated($applications, 'Danh sách đơn ứng tuyển.');
    }

    /**
     * View a specific application (candidate or HR/admin).
     *
     * GET /api/applications/{application}
     */
    public function show(Request $request, JobApplication $application): JsonResponse
    {
        $user = $request->user();

        $isOwner = $application->user_id === $user->id;
        $isAdmin = $user->role === 'admin';
        $isJobOwner = $application->jobPost?->user_id === $user->id;

        if (!$isOwner && !$isAdmin && !$isJobOwner) {
            return $this->error('Bạn không có quyền xem đơn ứng tuyển này.', 403);
        }

        $application->load(['jobPost', 'cv', 'user:id,name,email']);

        return $this->success(new JobApplicationResource($application), 'Chi tiết đơn ứng tuyển.');
    }

    /**
     * Admin/HR: List all applicants for a specific job.
     *
     * GET /api/admin/jobs/{jobPost}/applications
     */
    public function applicantsByJob(Request $request, JobPost $jobPost): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'admin' && $jobPost->user_id !== $user->id) {
            return $this->error('Bạn không có quyền xem danh sách ứng viên.', 403);
        }

        $query = JobApplication::with(['user:id,name,email,avatar', 'cv'])
            ->where('job_post_id', $jobPost->id)
            ->orderByDesc('applied_at');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(15);

        return $this->paginated($applications, 'Danh sách ứng viên.');
    }

    /**
     * Admin/HR: Update application status.
     *
     * PUT /api/admin/applications/{application}/status
     */
    public function updateStatus(UpdateApplicationStatusRequest $request, JobApplication $application): JsonResponse
    {
        $application->update($request->only(['status', 'notes']));

        return $this->success(
            new JobApplicationResource($application->fresh()->load(['jobPost', 'user'])),
            'Cập nhật trạng thái thành công!'
        );
    }

    /**
     * Admin: List all applications across all jobs.
     *
     * GET /api/admin/applications
     */
    public function adminIndex(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Chỉ admin mới có quyền truy cập.', 403);
        }

        $query = JobApplication::with(['user:id,name,email', 'jobPost:id,title,company_name'])
            ->orderByDesc('applied_at');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('job_post_id')) {
            $query->where('job_post_id', $request->job_post_id);
        }

        $applications = $query->paginate(15);

        return $this->paginated($applications, 'Danh sách tất cả đơn ứng tuyển (admin).');
    }

    /**
     * Admin/HR: Delete an application.
     *
     * DELETE /api/admin/applications/{application}
     */
    public function destroy(Request $request, JobApplication $application): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'admin' && $application->jobPost?->user_id !== $user->id) {
            return $this->error('Bạn không có quyền xóa đơn ứng tuyển này.', 403);
        }

        $application->deleteCvFile();
        $application->delete();

        return $this->success(null, 'Xóa đơn ứng tuyển thành công!');
    }
}
