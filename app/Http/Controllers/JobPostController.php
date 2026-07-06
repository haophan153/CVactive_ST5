<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPost;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::with('user')
            ->where('user_id', auth()->id());

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $jobPosts = $query->latest()->paginate(10);

        return view('hr.job-posts.index', compact('jobPosts'));
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

    public function show(JobPost $jobPost)
    {
        $this->authorizeJobPost($jobPost);

        return view('hr.job-posts.show', compact('jobPost'));
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
