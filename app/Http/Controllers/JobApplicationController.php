<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Cv;
use App\Services\PdfTextExtractor;

class JobApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['apply']);
    }

    /**
     * Trích xuất text từ PDF đã upload vào cột cv_text cho tất cả ứng viên của tin tuyển dụng
     */
    private function extractCvTextForJobPost(int $jobPostId): void
    {
        $appsWithoutText = JobApplication::where('job_post_id', $jobPostId)
            ->whereNotNull('cv_file')
            ->whereNull('cv_text')
            ->get();

        if ($appsWithoutText->isEmpty()) {
            return;
        }

        $extractor = new PdfTextExtractor();

        foreach ($appsWithoutText as $app) {
            $text = $extractor->extractFromFile($app->cv_file);
            if ($text) {
                $app->update(['cv_text' => $text]);
            }
        }
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
     * HR: Tìm kiếm CV theo kỹ năng/kinh nghiệm trong ứng viên của 1 tin tuyển dụng
     */
    public function searchCv(Request $request, JobPost $jobPost)
    {
        if ($jobPost->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập!');
        }

        $keyword = $request->get('q');

        // Trích xuất text từ PDF file upload cho các application chưa có cv_text
        $this->extractCvTextForJobPost($jobPost->id);

        $query = JobApplication::with(['user', 'cv.sections.items'])
            ->where('job_post_id', $jobPost->id);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                // Tìm trong thông tin cá nhân của ứng viên
                $q->where('full_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");

                // Tìm trong text đã trích xuất từ PDF file upload
                $q->orWhere('cv_text', 'like', "%{$keyword}%");

                // Tìm trong personal_info->skills của CV (dạng JSON array)
                $q->orWhereHas('cv', function ($cvq) use ($keyword) {
                    $cvq->where('personal_info->skills', 'like', "%{$keyword}%");
                });

                // Tìm trong nội dung CV sections/items
                $q->orWhereHas('cv.sections.items', function ($sq) use ($keyword) {
                    $sq->where(function ($sq2) use ($keyword) {
                        $sq2->orWhere('content->name', 'like', "%{$keyword}%");
                        $sq2->orWhere('content->position', 'like', "%{$keyword}%");
                        $sq2->orWhere('content->company', 'like', "%{$keyword}%");
                        $sq2->orWhere('content->description', 'like', "%{$keyword}%");
                        $sq2->orWhere('content->degree', 'like', "%{$keyword}%");
                        $sq2->orWhere('content->school', 'like', "%{$keyword}%");
                        $sq2->orWhere('content->tech', 'like', "%{$keyword}%");
                        $sq2->orWhere('content->url', 'like', "%{$keyword}%");
                    });
                });
            });
        }

        $applications = $query->latest('applied_at')->paginate(12)->withQueryString();

        return view('hr.applications.cv-search', compact('applications', 'jobPost', 'keyword'));
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

        // Search by applicant info
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
