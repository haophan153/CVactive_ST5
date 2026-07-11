<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\JobAlert;
use App\Models\JobMatchLog;
use App\Models\UploadedCv;
use App\Models\UserSkillProfile;
use App\Services\JobMatching\CvTextExtractor;
use App\Services\JobMatching\JobMatcherService;
use App\Services\JobMatching\SkillExtractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JobAlertController extends Controller
{
    public function __construct(
        private JobMatcherService $matcher,
        private SkillExtractor $extractor,
        private CvTextExtractor $textExtractor,
    ) {}

    /**
     * Hiển thị trang cài đặt Smart Job Matcher.
     */
    public function index(): View
    {
        $user = Auth::user();

        $alert = JobAlert::where('user_id', $user->id)->first();
        $profile = UserSkillProfile::where('user_id', $user->id)->first();
        $recentMatches = JobMatchLog::where('user_id', $user->id)
            ->whereNotNull('sent_at')
            ->with('jobPost')
            ->orderByDesc('sent_at')
            ->limit(5)
            ->get();

        return view('dashboard.job-alerts', [
            'alert' => $alert,
            'profile' => $profile,
            'recentMatches' => $recentMatches,
        ]);
    }

    /**
     * Lưu cài đặt thông báo việc làm.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_active'               => 'sometimes|boolean',
            'match_threshold'         => 'integer|min:1|max:100',
            'notification_frequency'   => 'in:daily,instant',
            'preferred_categories'     => 'sometimes|array',
            'preferred_categories.*'   => 'string',
            'preferred_job_types'     => 'sometimes|array',
            'preferred_job_types.*'   => 'string',
            'preferred_locations'      => 'sometimes|array',
            'preferred_locations.*'    => 'string|max:100',
            'notify_new_jobs'          => 'sometimes|boolean',
        ]);

        $alert = JobAlert::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'is_active'              => $validated['is_active'] ?? true,
                'match_threshold'        => $validated['match_threshold'] ?? 60,
                'notification_frequency'  => $validated['notification_frequency'] ?? 'daily',
                'preferred_categories'  => $validated['preferred_categories'] ?? null,
                'preferred_job_types'    => $validated['preferred_job_types'] ?? null,
                'preferred_locations'     => $validated['preferred_locations'] ?? null,
                'notify_new_jobs'        => $validated['notify_new_jobs'] ?? true,
            ]
        );

        return redirect()->route('dashboard.job-alerts')
            ->with('success', 'Đã lưu cài đặt Smart Job Matcher.');
    }

    /**
     * Xóa / tắt thông báo.
     */
    public function destroy(): RedirectResponse
    {
        JobAlert::where('user_id', Auth::id())->delete();

        return redirect()->route('dashboard.job-alerts')
            ->with('success', 'Đã tắt Smart Job Matcher.');
    }

    /**
     * AJAX toggle is_active (không reload trang).
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $alert = JobAlert::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'match_threshold' => 60,
                'notification_frequency' => 'daily',
                'notify_new_jobs' => true,
            ]
        );

        $alert->is_active = (bool) $validated['is_active'];
        $alert->save();

        return response()->json([
            'success' => true,
            'is_active' => $alert->is_active,
        ]);
    }

    /**
     * Upload CV (PDF/TXT) để AI scan skills.
     * Trả JSON với skills extracted + experience_level.
     */
    public function uploadCv(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'cv_file' => [
                    'required',
                    'file',
                    'max:' . (CvTextExtractor::MAX_FILE_SIZE / 1024),
                    'mimes:pdf,txt',
                ],
            ], [
                'cv_file.required' => 'Vui lòng chọn file CV.',
                'cv_file.file'     => 'File không hợp lệ.',
                'cv_file.max'      => 'File tối đa 5MB.',
                'cv_file.mimes'    => 'Chỉ chấp nhận file PDF hoặc TXT.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        }

        $file = $request->file('cv_file');
        $userId = Auth::id();

        // Lưu file
        $path = $file->store("uploaded-cvs/{$userId}", 'public');

        // Parse text
        $absolutePath = Storage::disk('public')->path($path);
        $text = $this->textExtractor->extract($absolutePath, $file->getMimeType() ?? '');

        if ($text === null || trim($text) === '') {
            Storage::disk('public')->delete($path);
            return response()->json([
                'success' => false,
                'message' => 'Không đọc được nội dung file. Vui lòng thử file PDF/TXT khác.',
            ], 422);
        }

        // Lưu bản ghi UploadedCv
        $uploaded = UploadedCv::create([
            'user_id'        => $userId,
            'original_name'  => $file->getClientOriginalName(),
            'file_path'      => $path,
            'mime_type'      => $file->getMimeType() ?? 'application/octet-stream',
            'file_size'      => $file->getSize(),
            'extracted_text' => $text,
            'parsed_at'      => now(),
        ]);

        // Extract skills qua AI (hoặc fallback dictionary)
        try {
            $extracted = $this->extractSkillsFromText($text);
        } catch (\Throwable $e) {
            Log::warning('uploadCv: skill extraction failed', ['error' => $e->getMessage()]);
            Storage::disk('public')->delete($path);
            $uploaded->delete();
            return response()->json([
                'success' => false,
                'message' => 'AI trích xuất thất bại. Vui lòng thử lại.',
            ], 500);
        }

        $skills = $extracted['skills'] ?? [];
        $expLevel = $extracted['experience_level'] ?? null;

        if (empty($skills)) {
            Storage::disk('public')->delete($path);
            $uploaded->delete();
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kỹ năng nào trong CV. Hãy đảm bảo CV có phần "Skills".',
            ], 422);
        }

        // Cập nhật UploadedCv
        $uploaded->update([
            'extracted_skills'  => $skills,
            'experience_level' => $expLevel,
        ]);

        // Cập nhật UserSkillProfile (dùng cho JobMatcherService)
        UserSkillProfile::updateOrCreate(
            ['user_id' => $userId],
            [
                'skills'             => $skills,
                'experience_level'   => $expLevel,
                'last_extracted_at'  => now(),
            ]
        );

        return response()->json([
            'success'           => true,
            'message'           => 'Đã phân tích ' . count($skills) . ' kỹ năng từ CV.',
            'skills'            => array_slice($skills, 0, 12),
            'total_skills'      => count($skills),
            'experience_level'  => $expLevel,
            'uploaded_cv_id'    => $uploaded->id,
            'file_name'         => $uploaded->original_name,
        ]);
    }

    /**
     * Trích xuất skills từ text CV thuần (không qua Cv model).
     */
    private function extractSkillsFromText(string $text): ?array
    {
        // Dùng reflection để gọi extractViaAi/extractViaDictionary từ SkillExtractor
        // mà không cần sửa service (Cv model).
        $reflection = new \ReflectionClass($this->extractor);
        $method = $reflection->getMethod('extractViaAi');
        $method->setAccessible(true);

        if ($this->extractor->isConfigured()) {
            $result = $method->invoke($this->extractor, $text);
            if ($result !== null) {
                return $result;
            }
        }

        $dictMethod = $reflection->getMethod('extractViaDictionary');
        $dictMethod->setAccessible(true);
        return $dictMethod->invoke($this->extractor, $text);
    }

    /**
     * Xóa CV đã upload.
     */
    public function deleteUploadedCv(int $id): JsonResponse
    {
        $cv = UploadedCv::where('user_id', Auth::id())->find($id);
        if (!$cv) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy CV.'], 404);
        }

        if (Storage::disk('public')->exists($cv->file_path)) {
            Storage::disk('public')->delete($cv->file_path);
        }
        $cv->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Trigger re-extract skills từ CV mới nhất.
     */
    public function extractSkills(): JsonResponse
    {
        $userId = Auth::id();

        $cv = Cv::where('user_id', $userId)
            ->where('is_draft', false)
            ->latest()
            ->first();

        if (!$cv) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa có CV nào để phân tích.',
            ], 422);
        }

        $profile = $this->extractor->extractAndSave($userId, $cv->id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể trích xuất kỹ năng từ CV. Vui lòng thử lại.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'skills_count' => count($profile->skills ?? []),
            'skills' => $profile->skills ?? [],
            'experience_level' => $profile->experience_level,
        ]);
    }

    /**
     * API: lấy job matches cho widget "Việc làm cho bạn".
     */
    public function apiMatches(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = min((int) $request->input('limit', 5), 10);

        $matches = $this->matcher->matchForWidget($user, $limit);

        $data = $matches->map(fn(JobMatchLog $log) => [
            'id'       => $log->job_post_id,
            'title'    => $log->jobPost->title,
            'company'  => $log->jobPost->company_name,
            'location' => $log->jobPost->location,
            'salary'   => $log->jobPost->salary_label,
            'score'    => $log->final_score,
            'url'      => $log->jobPost->share_url,
            'logo'     => $log->jobPost->company_logo_url,
            'is_new'   => $log->jobPost->is_new,
        ]);

        return response()->json(['matches' => $data]);
    }

    /**
     * API: đánh dấu match đã xem.
     */
    public function markViewed(int $jobId): JsonResponse
    {
        $log = JobMatchLog::where('user_id', Auth::id())
            ->where('job_post_id', $jobId)
            ->first();

        if ($log) {
            $log->update(['viewed_at' => now()]);
        }

        return response()->json(['success' => true]);
    }
}
