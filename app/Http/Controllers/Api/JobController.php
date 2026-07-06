<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreJobPostRequest;
use App\Http\Requests\Api\UpdateJobPostRequest;
use App\Http\Resources\JobPostResource;
use App\Models\JobPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobController extends ApiController
{
    /**
     * List all published jobs (public).
     *
     * GET /api/jobs
     *
     * Query params:
     *   keyword, location, min_salary, max_salary,
     *   job_type, experience_level, category, sort, page
     */
    public function index(Request $request): JsonResponse
    {
        $query = JobPost::with(['user:id,name,avatar'])
            ->published();

        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('min_salary')) {
            $query->where('salary_max', '>=', (int) $request->min_salary);
        }

        if ($request->filled('max_salary')) {
            $query->where('salary_min', '<=', (int) $request->max_salary);
        }

        if ($request->filled('job_type')) {
            $types = is_array($request->job_type) ? $request->job_type : explode(',', $request->job_type);
            $query->ofType($types);
        }

        if ($request->filled('experience_level')) {
            $levels = is_array($request->experience_level) ? $request->experience_level : explode(',', $request->experience_level);
            $query->atExperience($levels);
        }

        if ($request->filled('category')) {
            $cats = is_array($request->category) ? $request->category : explode(',', $request->category);
            $query->whereIn('category', $cats);
        }

        if ($request->input('is_remote') === '1') {
            $query->remote();
        }

        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'salary_high':  $query->orderByDesc('salary_max'); break;
            case 'salary_low':   $query->orderByRaw('salary_max IS NULL ASC, salary_max ASC'); break;
            case 'title_asc':   $query->orderBy('title'); break;
            case 'oldest':      $query->orderBy('published_at'); break;
            default:            $query->orderByDesc('published_at');
        }

        $perPage = min((int) $request->input('per_page', 12), 50);
        $jobs = $query->paginate($perPage);

        $jobs->getCollection()->transform(function ($job) {
            $job->applications_count = $job->applications()->count();
            return $job;
        });

        return $this->paginated($jobs, 'Danh sách tin tuyển dụng.');
    }

    /**
     * Get a single job detail (public).
     *
     * GET /api/jobs/{jobPost}
     */
    public function show(JobPost $jobPost): JsonResponse
    {
        $jobPost->load(['user:id,name,avatar,email']);
        $jobPost->applications_count = $jobPost->applications()->count();

        $jobPost->increment('views_count');

        return $this->success(new JobPostResource($jobPost), 'Chi tiết tin tuyển dụng.');
    }

    /**
     * Create a new job (admin only).
     *
     * POST /api/admin/jobs
     */
    public function store(StoreJobPostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
        }

        if ($data['status'] === 'published' && !$data['published_at'] ?? false) {
            $data['published_at'] = now();
        }

        $job = JobPost::create($data);

        return $this->success(
            new JobPostResource($job->load(['user:id,name,avatar'])),
            'Tạo tin tuyển dụng thành công!',
            201
        );
    }

    /**
     * Update a job (admin or owner HR).
     *
     * PUT /api/admin/jobs/{jobPost}
     */
    public function update(UpdateJobPostRequest $request, JobPost $jobPost): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('company_logo')) {
            if ($jobPost->company_logo) {
                Storage::disk('public')->delete($jobPost->company_logo);
            }
            $data['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
        }

        $jobPost->update($data);

        return $this->success(
            new JobPostResource($jobPost->fresh()->load(['user:id,name,avatar'])),
            'Cập nhật tin tuyển dụng thành công!'
        );
    }

    /**
     * Delete a job (admin only).
     *
     * DELETE /api/admin/jobs/{jobPost}
     */
    public function destroy(Request $request, JobPost $jobPost): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Chỉ admin mới có quyền xóa tin tuyển dụng.', 403);
        }

        if ($jobPost->company_logo) {
            Storage::disk('public')->delete($jobPost->company_logo);
        }

        $jobPost->delete();

        return $this->success(null, 'Xóa tin tuyển dụng thành công!');
    }

    /**
     * List all jobs for admin (all statuses).
     *
     * GET /api/admin/jobs
     */
    public function adminIndex(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Chỉ admin mới có quyền truy cập.', 403);
        }

        $query = JobPost::with(['user:id,name,avatar'])
            ->orderByDesc('created_at');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $jobs = $query->paginate(15);

        $jobs->getCollection()->transform(function ($job) {
            $job->applications_count = $job->applications()->count();
            return $job;
        });

        return $this->paginated($jobs, 'Danh sách tin tuyển dụng (admin).');
    }
}
