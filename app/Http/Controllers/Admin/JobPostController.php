<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::with('user', 'applications');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%$term%")
                  ->orWhere('company_name', 'like', "%$term%");
            });
        }

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('experience')) $query->where('experience_level', $request->experience);
        if ($request->filled('owner')) {
            if ($request->owner === 'none') $query->whereNull('user_id');
            else $query->where('user_id', $request->owner);
        }
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))   $query->whereDate('created_at', '<=', $request->to);

        $jobs = $query->latest()->paginate(20)->withQueryString();
        $owners = User::where('role', 'hr')->orWhereHas('jobPosts')->orderBy('name')->limit(100)->get();

        $stats = [
            'total'       => JobPost::count(),
            'open'        => JobPost::where('status', 'published')->count(),
            'closed'      => JobPost::where('status', 'closed')->count(),
            'draft'       => JobPost::where('status', 'draft')->count(),
            'applications' => \App\Models\JobApplication::count(),
        ];

        return view('admin.job-posts.index', compact('jobs', 'owners', 'stats'));
    }

    public function show(JobPost $jobPost)
    {
        $jobPost->load(['user', 'applications.user']);
        $topJobsByApps = JobPost::withCount('applications')
            ->orderByDesc('applications_count')
            ->limit(10)->get();

        return view('admin.job-posts.show', compact('jobPost', 'topJobsByApps'));
    }

    public function toggle(Request $request, JobPost $jobPost)
    {
        $request->validate([
            'status' => 'required|in:published,closed,draft',
        ]);
        $jobPost->update(['status' => $request->status]);
        return back()->with('success', 'Đã cập nhật trạng thái.');
    }

    public function destroy(JobPost $jobPost)
    {
        $jobPost->delete();
        return back()->with('success', 'Đã xóa tin tuyển dụng.');
    }
}
