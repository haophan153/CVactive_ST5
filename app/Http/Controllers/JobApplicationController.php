<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Cv;
use App\Services\PdfTextExtractor;
use App\Policies\ApplicationPolicy;

class JobApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['apply']);
    }

    // ============================================================
    // SECURE FILE DOWNLOAD - CRITICAL SECURITY METHOD
    // ============================================================

    /**
     * Tải file CV bảo mật
     *
     * SECURITY FLOW:
     * 1. Load application
     * 2. Authorize via ApplicationPolicy (checks ownership)
     * 3. Verify file exists
     * 4. Stream file via Storage::download (NOT direct URL)
     * 5. Log the access
     */
    public function downloadCv(JobApplication $application)
    {
        // ============================================================
        // STEP 1: Authentication check
        // ============================================================
        if (!auth()->check()) {
            abort(401, 'Vui lòng đăng nhập để tải CV.');
        }

        // ============================================================
        // STEP 2: Authorization via Policy
        // ============================================================
        $user = auth()->user();

        if (!Gate::forUser($user)->allows('downloadCv', $application)) {
            Log::channel('cv_access')->warning('CV Download DENIED - Unauthorized Access Attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'application_id' => $application->id,
                'candidate_name' => $application->full_name,
                'job_post_id' => $application->job_post_id,
                'job_post_owner_id' => $application->jobPost?->user_id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toIso8601String(),
            ]);

            abort(403, 'Bạn không có quyền tải file CV này.');
        }

        // ============================================================
        // STEP 3: Verify file exists
        // ============================================================
        if (!$application->hasCvFile()) {
            abort(404, 'File CV không tồn tại.');
        }

        // ============================================================
        // STEP 4: Get secure file path
        // ============================================================
        $securePath = $application->getSecureCvPath();

        if (!$securePath || !file_exists($securePath)) {
            Log::channel('cv_access')->error('CV File Missing', [
                'application_id' => $application->id,
                'cv_path' => $application->cv_path,
                'cv_file' => $application->cv_file,
                'attempted_path' => $securePath,
            ]);
            abort(404, 'File CV không tồn tại trên hệ thống.');
        }

        // ============================================================
        // STEP 5: Generate filename for download
        // ============================================================
        $downloadName = $this->sanitizeFilename(
            $application->full_name . '_' . $application->jobPost?->title . '_CV.pdf'
        );

        // ============================================================
        // STEP 6: Log successful access
        // ============================================================
        Log::channel('cv_access')->info('CV File Downloaded Successfully', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'application_id' => $application->id,
            'candidate_name' => $application->full_name,
            'candidate_email' => $application->email,
            'job_post_id' => $application->job_post_id,
            'job_post_title' => $application->jobPost?->title,
            'cv_path' => $application->cv_path ?? $application->cv_file,
            'download_name' => $downloadName,
            'file_size' => filesize($securePath),
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        // ============================================================
        // STEP 7: Stream file securely (NOT direct URL)
        // ============================================================
        return response()->download($securePath, $downloadName, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    // ============================================================
    // SECURE TEMPORARY SIGNED URL (Optional extra security)
    // ============================================================

    /**
     * Tạo temporary signed URL cho CV (expires in X minutes)
     * Dùng khi cần share link CV qua email cho recruiter
     */
    public function getSignedUrl(JobApplication $application)
    {
        if (!auth()->check()) {
            abort(401, 'Vui lòng đăng nhập.');
        }

        $user = auth()->user();

        if (!Gate::forUser($user)->allows('downloadCv', $application)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        if (!$application->hasCvFile()) {
            abort(404, 'File CV không tồn tại.');
        }

        // Tạo signed URL có hiệu lực trong 15 phút
        // Note: temporaryUrl chỉ hoạt động với S3 hoặc local disk có proper server config
        // Nếu không support, fallback về controller-based download
        $filePath = $application->cv_path ?? $application->cv_file;
        $signedUrl = null;

        try {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('local');
            $signedUrl = $disk->temporaryUrl(
                'private/' . $filePath,
                now()->addMinutes(15)
            );
        } catch (\Exception $e) {
            // Fallback: generate a temporary access token URL
            $signedUrl = route('hr.applications.cv.download', [
                'application' => $application->id,
                'token' => base64_encode(json_encode([
                    'exp' => now()->addMinutes(15)->timestamp,
                    'uid' => $user->id,
                ])),
            ]);
        }

        Log::channel('cv_access')->info('CV Signed URL Generated', [
            'user_id' => $user->id,
            'application_id' => $application->id,
            'expires_at' => now()->addMinutes(15)->toIso8601String(),
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'url' => $signedUrl,
            'expires_at' => now()->addMinutes(15)->toIso8601String(),
        ]);
    }

    // ============================================================
    // SECURE APPLICATION VIEW (Details)
    // ============================================================

    /**
     * Xem chi tiết ứng viên - bảo mật
     */
    public function hrShow(JobApplication $application)
    {
        $user = auth()->user();

        if (!Gate::forUser($user)->allows('view', $application)) {
            abort(403, 'Bạn không có quyền xem thông tin ứng viên này.');
        }

        return view('hr.applications.show', compact('application'));
    }

    /**
     * Cập nhật trạng thái ứng viên - bảo mật
     */
    public function updateStatus(Request $request, JobApplication $application)
    {
        $user = auth()->user();

        if (!Gate::forUser($user)->allows('updateStatus', $application)) {
            abort(403, 'Bạn không có quyền cập nhật trạng thái.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,approved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $application->update($validated);

        Log::channel('cv_access')->info('Application Status Updated', [
            'user_id' => $user->id,
            'application_id' => $application->id,
            'new_status' => $validated['status'],
            'ip' => request()->ip(),
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái!');
    }

    /**
     * Xóa đơn ứng tuyển - bảo mật
     */
    public function destroy(JobApplication $application)
    {
        $user = auth()->user();

        if (!Gate::forUser($user)->allows('delete', $application)) {
            abort(403, 'Bạn không có quyền xóa đơn ứng tuyển này.');
        }

        // Xóa file CV trước
        $application->deleteCvFile();

        // Xóa record
        $application->delete();

        Log::channel('cv_access')->info('Application Deleted', [
            'user_id' => $user->id,
            'application_id' => $application->id,
            'candidate_name' => $application->full_name,
            'ip' => request()->ip(),
        ]);

        return redirect()->route('hr.applications.index')
            ->with('success', 'Đã xóa đơn ứng tuyển!');
    }

    // ============================================================
    // SECURE CV SEARCH
    // ============================================================

    /**
     * Tìm kiếm CV theo kỹ năng/kinh nghiệm - bảo mật
     */
    public function searchCv(Request $request, JobPost $jobPost)
    {
        $user = auth()->user();

        if (!Gate::forUser($user)->allows('searchCv', $jobPost)) {
            abort(403, 'Bạn không có quyền truy cập!');
        }

        $keyword = $request->get('q');

        // Trích xuất text từ PDF
        $this->extractCvTextForJobPost($jobPost->id);

        $query = JobApplication::with(['user', 'cv.sections.items'])
            ->where('job_post_id', $jobPost->id);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('full_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('cv_text', 'like', "%{$keyword}%");

                $q->orWhereHas('cv', function ($cvq) use ($keyword) {
                    $cvq->where('personal_info->skills', 'like', "%{$keyword}%");
                });

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

    // ============================================================
    // SECURE APPLICATIONS LIST
    // ============================================================

    /**
     * Ứng viên theo từng bài đăng - bảo mật
     */
    public function hrApplicationsByJob(Request $request, JobPost $jobPost)
    {
        $user = auth()->user();

        if (!Gate::forUser($user)->allows('viewApplications', $jobPost)) {
            abort(403, 'Bạn không có quyền truy cập!');
        }

        $query = JobApplication::with(['user', 'cv'])
            ->where('job_post_id', $jobPost->id);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

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
     * Danh sách tất cả ứng viên - bảo mật
     */
    public function hrIndex(Request $request)
    {
        $user = auth()->user();

        // Chỉ HR mới được truy cập dashboard này
        if (!$user->isHR() && $user->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Chỉ lấy applications thuộc job posts của HR này
        $jobPostIds = JobPost::where('user_id', $user->id)->pluck('id');

        $query = JobApplication::with(['jobPost', 'user', 'cv'])
            ->whereIn('job_post_id', $jobPostIds);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->job_post_id) {
            // Security: Chỉ cho filter theo job posts mà user sở hữu
            if (JobPost::where('id', $request->job_post_id)->where('user_id', $user->id)->exists()) {
                $query->where('job_post_id', $request->job_post_id);
            }
        }

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

    // ============================================================
    // USER'S OWN APPLICATIONS
    // ============================================================

    /**
     * Lịch sử ứng tuyển của user - chỉ user đó được xem
     */
    public function myApplications()
    {
        $applications = JobApplication::with('jobPost')
            ->where('user_id', auth()->id())
            ->latest('applied_at')
            ->paginate(10);

        return view('user.applications.index', compact('applications'));
    }

    // ============================================================
    // PUBLIC JOB APPLICATION
    // ============================================================

    /**
     * Ứng tuyển công việc - với bảo mật file upload
     */
    public function apply(Request $request, JobPost $jobPost)
    {
        // Validate với bảo mật
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'cv_id' => 'nullable|exists:cvs,id',
            'cv_file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120', // 5MB max
            ],
            'cover_letter' => 'nullable|string|max:2000',
        ]);

        // Check if job post is still accepting applications
        if ($jobPost->status !== 'published') {
            return back()->with('error', 'Tin tuyển dụng này không còn nhận hồ sơ.')->withInput();
        }

        // Check duplicate application
        if (auth()->check()) {
            $existingApplication = JobApplication::where('job_post_id', $jobPost->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($existingApplication) {
                return back()->with('error', 'Bạn đã ứng tuyển vị trí này rồi!')->withInput();
            }
        } else {
            $existingApplication = JobApplication::where('job_post_id', $jobPost->id)
                ->where('email', $validated['email'])
                ->first();

            if ($existingApplication) {
                return back()->with('error', 'Email này đã ứng tuyển vị trí này rồi!')->withInput();
            }
        }

        // ============================================================
        // SECURE FILE UPLOAD - Store in PRIVATE disk
        // ============================================================
        $cvPath = null;

        if ($request->hasFile('cv_file')) {
            $file = $request->file('cv_file');

            // Generate unique filename to prevent collisions
            $filename = sprintf(
                '%s_%s_%s.%s',
                Str::slug($validated['full_name']),
                $jobPost->id,
                Str::random(8),
                $file->getClientOriginalExtension()
            );

            // Store in PRIVATE disk (NOT public)
            $cvPath = $file->storeAs(
                'applications/' . $jobPost->id,
                $filename,
                'local' // Using local disk with storage/app/private
            );

            Log::channel('cv_access')->info('CV File Uploaded', [
                'user_id' => auth()->check() ? auth()->id() : null,
                'application_email' => $validated['email'],
                'job_post_id' => $jobPost->id,
                'filename' => $filename,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'ip' => request()->ip(),
            ]);
        }

        // Create application record
        $applicationData = [
            'job_post_id' => $jobPost->id,
            'user_id' => auth()->check() ? auth()->id() : null,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'cv_file' => null, // Keep old field null for new uploads
            'cv_path' => $cvPath, // Use new secure path field
            'cv_id' => $validated['cv_id'] ?? null,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'applied_at' => now(),
            'status' => 'pending',
        ];

        $application = JobApplication::create($applicationData);

        // Extract CV text in background (if PDF)
        if ($cvPath && $request->hasFile('cv_file')) {
            $this->extractCvTextForApplication($application);
        }

        return back()->with('success', 'Đã nộp hồ sơ ứng tuyển thành công!');
    }

    // ============================================================
    // PRIVATE HELPER METHODS
    // ============================================================

    /**
     * Trích xuất text từ PDF đã upload
     * @param int $jobPostId
     */
    private function extractCvTextForJobPost(int $jobPostId): void
    {
        /** @var \Illuminate\Support\Collection<int, JobApplication> $appsWithoutText */
        $appsWithoutText = JobApplication::where('job_post_id', $jobPostId)
            ->whereNotNull('cv_path')
            ->whereNull('cv_text')
            ->get();

        foreach ($appsWithoutText as $app) {
            $this->extractCvTextForApplication($app);
        }
    }

    /**
     * Trích xuất text từ PDF của một application cụ thể
     * @param JobApplication $application
     */
    private function extractCvTextForApplication(JobApplication $application): void
    {
        if (!$application->cv_path) {
            return;
        }

        try {
            $extractor = new PdfTextExtractor();
            $text = $extractor->extractFromFile($application->cv_path);

            if ($text) {
                $application->update(['cv_text' => $text]);
            }
        } catch (\Exception $e) {
            Log::error('CV Text Extraction Failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Sanitize filename để ngăn chặn path traversal
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove any path components
        $filename = basename($filename);

        // Replace spaces with underscores
        $filename = str_replace(' ', '_', $filename);

        // Remove any characters that aren't alphanumeric, dash, underscore, or dot
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename);

        // Ensure it ends with .pdf
        if (!Str::endsWith(strtolower($filename), '.pdf')) {
            $filename .= '.pdf';
        }

        return $filename;
    }
}
