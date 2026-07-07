<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JobPost;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::with('user')
            ->where('user_id', auth()->id());

        // Free-text search
        if ($request->filled('q')) {
            $term = trim((string) $request->q);
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(title) LIKE ?', ['%'.mb_strtolower($term).'%'])
                  ->orWhereRaw('LOWER(company_name) LIKE ?', ['%'.mb_strtolower($term).'%'])
                  ->orWhereRaw('LOWER(location) LIKE ?', ['%'.mb_strtolower($term).'%']);
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('is_hot')) {
            $query->where('is_hot', true);
        }
        if ($request->filled('is_remote')) {
            $query->where('is_remote', true);
        }

        // Sort
        switch ($request->input('sort', 'newest')) {
            case 'oldest':       $query->oldest(); break;
            case 'title_asc':    $query->orderBy('title'); break;
            case 'title_desc':   $query->orderByDesc('title'); break;
            case 'most_applied': $query->withCount('applications')->orderByDesc('applications_count'); break;
            case 'most_viewed':  $query->orderByDesc('views_count'); break;
            case 'newest':       $query->latest('published_at')->latest(); break;
            default:             $query->latest();
        }

        $jobPosts = $query->paginate(12)->withQueryString();

        // Aggregate stats for dashboard header (all of the HR's posts, ignoring filters)
        $allPosts = JobPost::where('user_id', auth()->id())->withCount('applications');
        $stats = [
            'total'        => (clone $allPosts)->count(),
            'published'    => (clone $allPosts)->where('status', 'published')->count(),
            'drafts'       => (clone $allPosts)->where('status', 'draft')->count(),
            'closed'       => (clone $allPosts)->where('status', 'closed')->count(),
            'applications' => (clone $allPosts)->get()->sum('applications_count'),
            'views'        => (clone $allPosts)->sum('views_count'),
            'hot'          => (clone $allPosts)->where('is_hot', true)->count(),
            'remote'       => (clone $allPosts)->where('is_remote', true)->count(),
            'expiring'     => (clone $allPosts)
                                  ->where('status', 'published')
                                  ->whereNotNull('expires_at')
                                  ->whereBetween('expires_at', [now(), now()->addDays(7)])
                                  ->count(),
        ];

        // Last 14 days applications chart (across all HR posts)
        $start = now()->subDays(13)->startOfDay();
        $applicationsByDay = \App\Models\JobApplication::query()
            ->whereHas('jobPost', fn ($q) => $q->where('user_id', auth()->id()))
            ->select(DB::raw('DATE(applied_at) as d'), DB::raw('COUNT(*) as c'))
            ->where('applied_at', '>=', $start)
            ->groupBy('d')
            ->pluck('c', 'd');

        $timeline = collect(range(13, 0))->map(function ($ago) use ($applicationsByDay) {
            $date = now()->subDays($ago)->format('Y-m-d');
            return (object) ['date' => $date, 'count' => (int) ($applicationsByDay[$date] ?? 0)];
        });

        return view('hr.job-posts.index', [
            'jobPosts'    => $jobPosts,
            'stats'       => $stats,
            'timeline'    => $timeline,
            'categories'  => JobPost::CATEGORIES,
            'jobTypes'    => JobPost::JOB_TYPES,
            'filters'     => $request->only(['q', 'status', 'job_type', 'category', 'is_hot', 'is_remote', 'sort']),
        ]);
    }

    /**
     * Realtime dashboard heartbeat (JSON).
     */
    public function heartbeat(): \Illuminate\Http\JsonResponse
    {
        $base = JobPost::where('user_id', auth()->id())->withCount('applications');
        $apps = (clone $base)->get()->sum('applications_count');

        return response()->json([
            'stats' => [
                'total'        => (clone $base)->count(),
                'published'    => (clone $base)->where('status', 'published')->count(),
                'drafts'       => (clone $base)->where('status', 'draft')->count(),
                'closed'       => (clone $base)->where('status', 'closed')->count(),
                'applications' => $apps,
                'views'        => (clone $base)->sum('views_count'),
                'hot'          => (clone $base)->where('is_hot', true)->count(),
                'remote'       => (clone $base)->where('is_remote', true)->count(),
                'expiring'     => (clone $base)
                                       ->where('status', 'published')
                                       ->whereNotNull('expires_at')
                                       ->whereBetween('expires_at', [now(), now()->addDays(7)])
                                       ->count(),
                'updated_at'   => optional((clone $base)->max('updated_at'))->toIso8601String(),
            ],
            'server_ts' => now()->toIso8601String(),
        ])->header('Cache-Control', 'no-store');
    }

    public function create()
    {
        return view('hr.job-posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'job_type' => 'nullable|string|in:full-time,part-time,contract,internship,remote',
            'category' => 'nullable|string|max:50',
            'experience_level' => 'nullable|string|in:fresher,junior,middle,senior,lead',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => 'nullable|integer|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|max:10',
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'company_logo' => 'nullable|image|max:2048',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'expires_at' => 'nullable|date|after:today',
            'is_remote' => 'nullable|boolean',
            'is_hot' => 'nullable|boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'draft';
        $validated['is_remote'] = $request->boolean('is_remote');
        $validated['is_hot'] = $request->boolean('is_hot');

        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
        }

        JobPost::create($validated);

        return redirect()->route('hr.job-posts.index')
            ->with('success', 'Bài đăng đã được tạo thành công!');
    }

    public function show(Request $request, JobPost $jobPost)
    {
        $this->authorizeJobPost($jobPost);

        $jobPost->loadCount('applications');
        $jobPost->load(['applications' => function ($q) {
            $q->latest('applied_at')->limit(8);
        }, 'applications.user']);

        // Aggregate counts by application status
        $appStats = [
            'total'     => $jobPost->applications()->count(),
            'pending'   => $jobPost->applications()->where('status', 'pending')->count(),
            'reviewing' => $jobPost->applications()->where('status', 'reviewing')->count(),
            'approved'  => $jobPost->applications()->where('status', 'approved')->count(),
            'rejected'  => $jobPost->applications()->where('status', 'rejected')->count(),
        ];

        // 14-day views (using last_saved_at as proxy if not tracked)
        $days = collect(range(13, 0))->map(function ($ago) {
            $date = now()->subDays($ago)->format('Y-m-d');
            return (object) ['date' => $date, 'count' => 0];
        });

        return view('hr.job-posts.show', [
            'jobPost'   => $jobPost,
            'appStats'  => $appStats,
            'daysChart' => $days,
        ]);
    }

    public function edit(JobPost $jobPost)
    {
        $this->authorizeJobPost($jobPost);

        return view('hr.job-posts.edit', compact('jobPost'));
    }

    public function update(Request $request, JobPost $jobPost)
    {
        $this->authorizeJobPost($jobPost);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'job_type' => 'nullable|string|in:full-time,part-time,contract,internship,remote',
            'category' => 'nullable|string|max:50',
            'experience_level' => 'nullable|string|in:fresher,junior,middle,senior,lead',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => 'nullable|integer|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|max:10',
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'company_logo' => 'nullable|image|max:2048',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'expires_at' => 'nullable|date',
            'is_remote' => 'nullable|boolean',
            'is_hot' => 'nullable|boolean',
        ]);

        $validated['is_remote'] = $request->boolean('is_remote');
        $validated['is_hot'] = $request->boolean('is_hot');

        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
        }

        $jobPost->update($validated);

        return redirect()->route('hr.job-posts.show', $jobPost)
            ->with('success', 'Bài đăng đã được cập nhật!');
    }

    public function destroy(JobPost $jobPost)
    {
        $this->authorizeJobPost($jobPost);

        $jobPost->delete();

        return redirect()->route('hr.job-posts.index')
            ->with('success', 'Bài đăng đã được xóa!');
    }

    public function publish(JobPost $jobPost)
    {
        $this->authorizeJobPost($jobPost);

        $jobPost->publish();

        return back()->with('success', 'Bài đăng đã được đăng tải!');
    }

    public function close(JobPost $jobPost)
    {
        $this->authorizeJobPost($jobPost);

        $jobPost->close();

        return back()->with('success', 'Bài đăng đã được đóng!');
    }

    // Public methods - for job listing page
    public function publicIndex(Request $request)
    {
        $query = JobPost::with('user')
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%")
                    ->orWhere('company_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->location) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->type) {
            $query->where('job_type', $request->type);
        }

        $jobPosts = $query->latest('published_at')->paginate(12);

        return view('jobs.index', compact('jobPosts'));
    }

    public function publicShow(JobPost $jobPost)
    {
        if (!$jobPost->isPublished() && (!auth()->check() || $jobPost->user_id !== auth()->id())) {
            abort(404);
        }

        $jobPost->increment('views_count');

        return view('jobs.show', compact('jobPost'));
    }

    private function authorizeJobPost(JobPost $jobPost)
    {
        if ($jobPost->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập bài đăng này.');
        }
    }
}
