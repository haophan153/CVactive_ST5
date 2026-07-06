<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;

/**
 * Controller for the public Job Listing page.
 * Uses the same filtering logic as Api\JobController but returns Blade view.
 */
class JobListingController extends Controller
{
    // Predefined filter options (shared between controller and view)
    public static function filterOptions(): array
    {
        return [
            'job_types' => [
                'full-time' => 'Toàn thời gian',
                'part-time' => 'Bán thời gian',
                'contract' => 'Hợp đồng',
                'internship' => 'Thực tập',
                'remote' => 'Remote / Từ xa',
            ],
            'experience_levels' => [
                'fresher' => 'Fresher (0-1 năm)',
                'junior' => 'Junior (1-3 năm)',
                'middle' => 'Middle (3-5 năm)',
                'senior' => 'Senior (5+ năm)',
                'lead' => 'Lead / Manager',
            ],
            'categories' => [
                'it' => 'Công nghệ thông tin',
                'marketing' => 'Marketing',
                'design' => 'Thiết kế',
                'finance' => 'Tài chính / Kế toán',
                'hr' => 'Nhân sự',
                'sales' => 'Kinh doanh / Bán hàng',
                'operation' => 'Vận hành / QA',
                'consulting' => 'Tư vấn',
                'education' => 'Giáo dục / Đào tạo',
                'other' => 'Khác',
            ],
            'sort_options' => [
                'newest' => 'Mới nhất',
                'oldest' => 'Cũ nhất',
                'salary_high' => 'Lương cao → thấp',
                'salary_low' => 'Lương thấp → cao',
                'title' => 'Theo tên A → Z',
            ],
            'salary_ranges' => [
                0 => 'Tất cả mức lương',
                5000000 => 'Từ 5 triệu',
                10000000 => 'Từ 10 triệu',
                15000000 => 'Từ 15 triệu',
                20000000 => 'Từ 20 triệu',
                30000000 => 'Từ 30 triệu',
                50000000 => 'Từ 50 triệu',
            ],
        ];
    }

    /**
     * Render the job listing page with filters.
     * GET /jobs
     */
    public function index(Request $request)
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
            case 'title_asc':    $query->orderBy('title'); break;
            case 'oldest':       $query->orderBy('published_at'); break;
            default:             $query->orderByDesc('published_at');
        }

        $perPage = min((int) $request->input('per_page', 12), 50);
        $jobPosts = $query->paginate($perPage);
        $jobPosts->getCollection()->transform(fn($job) => $job->setRelation('applications', $job->applications()->get()));

        $totalJobs = JobPost::published()->count();
        $totalCompanies = JobPost::published()->distinct('company_name')->count('company_name');

        return view('jobs.index', [
            'jobPosts' => $jobPosts,
            'filters' => $request->only([
                'keyword', 'location', 'min_salary', 'max_salary',
                'job_type', 'experience_level', 'category', 'sort', 'is_remote'
            ]),
            'filterOptions' => [
                'sort_options' => JobPost::SORT_OPTIONS,
                'salary_ranges' => JobPost::SALARY_RANGES,
            ],
            'totalJobs' => $totalJobs,
            'totalCompanies' => $totalCompanies,
        ]);
    }

    /**
     * Job detail page (public).
     * GET /jobs/{id}
     */
    public function show(JobPost $jobPost)
    {
        $jobPost->load(['user:id,name,avatar,email']);
        $jobPost->applications_count = $jobPost->applications()->count();
        $jobPost->increment('views_count');

        // Related jobs (same category, excluding current)
        $relatedJobs = JobPost::published()
            ->where('id', '!=', $jobPost->id)
            ->when($jobPost->category, fn($q) => $q->where('category', $jobPost->category))
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        return view('jobs.show', compact('jobPost', 'relatedJobs'));
    }
}
