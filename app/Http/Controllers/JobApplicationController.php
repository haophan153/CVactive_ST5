<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Cv;

class JobApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['apply']);
    }

    /**
     * Ứng tuyển công việc
     */
    public function apply(Request $request, JobPost $jobPost)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'cv_id' => 'nullable|exists:cvs,id',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|string',
        ]);

        // Check if user already applied to this job
        if (auth()->check()) {
            $existingApplication = JobApplication::where('job_post_id', $jobPost->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($existingApplication) {
                return back()->with('error', 'Bạn đã ứng tuyển vị trí này rồi!')->withInput();
            }
        } else {
            // Guest - check by email
            $existingApplication = JobApplication::where('job_post_id', $jobPost->id)
                ->where('email', $validated['email'])
                ->first();

            if ($existingApplication) {
                return back()->with('error', 'Email này đã ứng tuyển vị trí này rồi!')->withInput();
            }
        }

        // Handle CV file upload
        if ($request->hasFile('cv_file')) {
            $validated['cv_file'] = $request->file('cv_file')->store('applications', 'public');
        }

        $validated['job_post_id'] = $jobPost->id;
        $validated['user_id'] = auth()->check() ? auth()->id() : null;
        $validated['applied_at'] = now();

        JobApplication::create($validated);

        return back()->with('success', 'Đã nộp hồ sơ ứng tuyển thành công!');
    }

    /**
     * HR: Ứng viên theo từng bài đăng
     */
    public function hrApplicationsByJob(Request $request, JobPost $jobPost)
    {
        // Verify ownership
        if ($jobPost->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập!');
        }

        $query = JobApplication::with(['user', 'cv'])
            ->where('job_post_id', $jobPost->id);

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $applications = $query->latest('applied_at')->paginate(15);

        return view('hr.applications.by-job', compact('applications', 'jobPost'));
    }

    /**
     * HR: Danh sách tất cả ứng viên
     */
    public function hrIndex(Request $request)
    {
        $user = auth()->user();
        
        // Get job posts owned by this HR
        $jobPostIds = JobPost::where('user_id', $user->id)->pluck('id');

        $query = JobApplication::with(['jobPost', 'user', 'cv'])
            ->whereIn('job_post_id', $jobPostIds);

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by job post
        if ($request->job_post_id) {
            $query->where('job_post_id', $request->job_post_id);
        }

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $applications = $query->latest('applied_at')->paginate(15);
        
        $jobPosts = JobPost::where('user_id', $user->id)
            ->where('status', 'published')
            ->orderBy('title')
            ->get();

        return view('hr.applications.index', compact('applications', 'jobPosts'));
    }

    /**
     * HR: Xem chi tiết ứng viên
     */
    public function hrShow(JobApplication $application)
    {
        $this->authorizeApplication($application);

        return view('hr.applications.show', compact('application'));
    }

    /**
     * HR: Cập nhật trạng thái ứng viên
     */
    public function updateStatus(Request $request, JobApplication $application)
    {
        $this->authorizeApplication($application);

        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $application->update($validated);

        return back()->with('success', 'Đã cập nhật trạng thái!');
    }

    /**
     * HR: Xóa đơn ứng tuyển
     */
    public function destroy(JobApplication $application)
    {
        $this->authorizeApplication($application);

        // Delete CV file if exists
        if ($application->cv_file && \Storage::disk('public')->exists($application->cv_file)) {
            \Storage::disk('public')->delete($application->cv_file);
        }

        $application->delete();

        return redirect()->route('hr.applications.index')
            ->with('success', 'Đã xóa đơn ứng tuyển!');
    }

    /**
     * Lịch sử ứng tuyển của user
     */
    public function myApplications()
    {
        $applications = JobApplication::with('jobPost')
            ->where('user_id', auth()->id())
            ->latest('applied_at')
            ->paginate(10);

        return view('user.applications.index', compact('applications'));
    }

    private function authorizeApplication(JobApplication $application)
    {
        $jobPost = $application->jobPost;
        if ($jobPost->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập!');
        }
    }
}
