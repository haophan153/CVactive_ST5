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
use App\Models\Template;
use App\Services\PdfTextExtractor;
use App\Policies\ApplicationPolicy;
use App\Support\PiiLogHash;
use Illuminate\Validation\Rule;

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
        // Eager load jobPost + user để tránh lazy-load khi check authorization
        // ============================================================
        $application->loadMissing('jobPost.user');
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
        // L-6: hash email trước khi log — chống GDPR leak qua backup/log shipper
        Log::channel('cv_access')->info('CV File Downloaded Successfully', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'application_id' => $application->id,
            'candidate_name' => $application->full_name,
            'candidate_email_hash' => PiiLogHash::email($application->email),
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

        // Eager load jobPost + user để tránh lazy-load khi check authorization
        $application->loadMissing('jobPost.user');
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

        // Eager load jobPost + user để tránh lazy-load khi check authorization
        $application->loadMissing('jobPost.user');

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

        // Eager load jobPost + user để tránh lazy-load khi check authorization
        $application->loadMissing('jobPost.user');

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

        // Eager load jobPost + user để tránh lazy-load khi check authorization
        $application->loadMissing('jobPost.user');

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
     * Trích xuất toàn bộ text từ CV ứng viên (để search)
     */
    private function extractCvTextForSearch(Cv $cv): string
    {
        $texts = [];

        // personal_info
        $pi = $cv->personal_info ?? [];
        foreach ($pi as $val) {
            if (is_string($val) && $val) $texts[] = $val;
        }

        // objective
        if (!empty($cv->objective)) $texts[] = $cv->objective;

        // sections & items
        foreach ($cv->sections as $section) {
            foreach ($section->items as $item) {
                foreach (($item->content ?? []) as $val) {
                    if (is_string($val) && $val) $texts[] = $val;
                }
            }
        }

        return mb_strtolower(implode(' ', $texts));
    }

    /**
     * Tìm kiếm & xếp hạng CV theo multi-keyword
     */
    public function searchCv(Request $request, JobPost $jobPost)
    {
        $user = auth()->user();
        $jobPost->loadMissing('user');

        if (!Gate::forUser($user)->allows('searchCv', $jobPost)) {
            abort(403, 'Bạn không có quyền truy cập!');
        }

        $this->extractCvTextForJobPost($jobPost->id);

        $keywordInput = $request->input('keywords', '');

        // Parse keywords: split by comma, newline, hoặc space
        $keywords = array_filter(
            array_map('trim', preg_split('/[,\n]+/', $keywordInput)),
            fn($k) => mb_strlen($k) >= 2
        );
        $keywords = array_map('mb_strtolower', $keywords);

        $applications = JobApplication::with(['user', 'cv.sections.items'])
            ->where('job_post_id', $jobPost->id)
            ->get();

        // Nếu có keywords → tính score & lọc
        if (!empty($keywords)) {
            /** @var array<int, JobApplication> $scored */
            $scored = [];

            foreach ($applications as $app) {
                $appCvText = '';

                if ($app->cv) {
                    $appCvText = $this->extractCvTextForSearch($app->cv);
                } elseif ($app->cv_text) {
                    $appCvText = mb_strtolower($app->cv_text);
                }

                $matched = [];
                $missing = [];
                $score = 0;

                foreach ($keywords as $kw) {
                    if (str_contains($appCvText, $kw)) {
                        $matched[] = $kw;
                        $score++;
                    } else {
                        $missing[] = $kw;
                    }
                }

                // Chỉ hiện CV có ít nhất 1 keyword match
                if ($score > 0) {
                    $app->keyword_score = $score;
                    $app->keyword_matched = $matched;
                    $app->keyword_missing = $missing;
                    $scored[] = $app;
                }
            }

            // Sort: nhiều match nhất → ít nhất; cùng score thì theo applied_at
            usort($scored, function ($a, $b) use ($keywords) {
                if ($b->keyword_score !== $a->keyword_score) {
                    return $b->keyword_score <=> $a->keyword_score;
                }
                // Tie-break: ưu tiên keyword gần cuối (keyword đặc thù hơn)
                $aLastScore = 0;
                $bLastScore = 0;
                foreach (array_reverse($keywords) as $ki => $kw) {
                    if (in_array($kw, $a->keyword_matched)) $aLastScore = $ki;
                    if (in_array($kw, $b->keyword_matched)) $bLastScore = $ki;
                }
                return $bLastScore <=> $aLastScore;
            });

            $total = count($scored);
            $perPage = 12;
            $page = (int) $request->input('page', 1);
            $paginated = array_slice($scored, ($page - 1) * $perPage, $perPage);

            $applications = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginated,
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Không có keyword → hiện tất cả, sắp xếp theo applied_at
            $applications = $applications
                ->sortByDesc('applied_at')
                ->values();
            $total = $applications->count();
            $perPage = 12;
            $page = (int) $request->input('page', 1);
            $paginated = $applications->slice(($page - 1) * $perPage, $perPage);
            $applications = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginated, $total, $perPage, $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        return view('hr.applications.cv-search', [
            'applications' => $applications,
            'jobPost' => $jobPost,
            'keywords' => $keywordInput,
            'keywordList' => $keywords,
        ]);
    }

    // ============================================================
    // SECURE APPLICATIONS LIST
    // ============================================================

    /**
     * Ứng viên theo từng bài đăng - bảo mật
     */
    public function hrApplicationsByJob(Request $request, JobPost $jobPost)
    {
        $user = $request->user();

        // Eager load user để tránh lazy-load khi check authorization
        $jobPost->loadMissing('user');

        if (!Gate::forUser($user)->allows('viewApplications', $jobPost)) {
            abort(403, 'Bạn không có quyền truy cập!');
        }

        $query = JobApplication::with(['user', 'cv', 'jobPost'])
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

        // Sắp xếp: mặc định mới nhất; ?sort=ai => điểm AI cao → thấp (null cuối)
        $sort = $request->input('sort', 'newest');
        if ($sort === 'ai') {
            $query->orderByRaw('ai_score IS NULL ASC')
                ->orderByDesc('ai_score')
                ->orderByDesc('applied_at');
        } elseif ($sort === 'oldest') {
            $query->orderBy('applied_at', 'asc');
        } else {
            $query->latest('applied_at');
        }

        $applications = $query->paginate(15)->withQueryString();

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
     * Lịch sử ứng tuyển của user - chỉ user đó được xem.
     *
     * M1: Ẩn các field nhạy cảm (ai_breakdown, ai_summary chi tiết) để
     * applicant không thấy lý do HR reject. Chỉ trả score (int) + label.
     */
    public function myApplications()
    {
        $applications = JobApplication::with('jobPost')
            ->where('user_id', auth()->id())
            ->latest('applied_at')
            ->paginate(10);

        // M1: Mask các field nhạy cảm — applicant không được xem breakdown
        $applications->getCollection()->transform(function ($app) {
            $app->setAttribute('ai_breakdown', null);
            $app->setAttribute('ai_summary', null);
            return $app;
        });

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
            // L-12: giới hạn cv_id về user hiện tại — nếu attacker gửi cv_id
            // của user khác, bị reject ngay tại FormRequest.
            'cv_id' => [
                'nullable',
                'integer',
                Rule::exists('cvs', 'id')->where(function ($query) use ($request) {
                    $query->where('user_id', $request->user()?->id);
                }),
            ],
            'cv_file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120', // 5MB max
                // C3: kiểm tra MIME thật qua finfo — chống file PHP rename .pdf
                function (string $attribute, $value, \Closure $fail) {
                    if (!$value || !method_exists($value, 'getRealPath')) return;
                    $realPath = $value->getRealPath();
                    if (!$realPath || !is_readable($realPath)) return;
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->file($realPath);
                    $allowed = ['application/pdf', 'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                    if (!in_array($mime, $allowed, true)) {
                        $fail("File CV phải là PDF, DOC hoặc DOCX thực sự (MIME: {$mime}).");
                    }
                },
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
                'application_email_hash' => PiiLogHash::email($validated['email']),
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

        // C2: Extract CV text in background via queue (chống DoS qua PDF lớn)
        if ($cvPath && $request->hasFile('cv_file')) {
            \App\Jobs\ExtractCvTextJob::dispatch($application->id);
        }

        return back()->with('success', 'Đã nộp hồ sơ ứng tuyển thành công!');
    }

    // ============================================================
    // PRIVATE HELPER METHODS
    // ============================================================

    /**
     * Đẩy các application chưa extract text vào queue (không chạy sync).
     *
     * C2 + C5: trước đây chạy đồng bộ trong request lifecycle → nếu HR spam
     * searchCv, mỗi lần sẽ trigger extract PDF cho TẤT CẢ application
     * chưa có text → DoS. Giờ dispatch job, worker xử lý sau.
     *
     * C5: throttle IP/user ở route layer đã có; controller chỉ dispatch
     * job để tránh block request.
     */
    private function extractCvTextForJobPost(int $jobPostId): void
    {
        $appIds = JobApplication::where('job_post_id', $jobPostId)
            ->whereNotNull('cv_path')
            ->whereNull('cv_text')
            ->limit(50)   // Cap mỗi lần để tránh flood queue
            ->pluck('id');

        foreach ($appIds as $id) {
            \App\Jobs\ExtractCvTextJob::dispatch($id);
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
