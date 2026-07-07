<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Services\CvScoring\CvScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

/**
 * Bulk-score / re-score ứng viên bằng AI.
 *
 *  - bulkScore($jobPost): chấm điểm tất cả đơn (chỉ những cái chưa có điểm, trừ khi ?rescore=1).
 *  - rescore($application): chấm lại 1 đơn.
 */
class AiScoreController extends Controller
{
    public function __construct(private CvScoringService $scorer) {}

    /**
     * POST hr/job-posts/{jobPost}/ai-score
     * Optional: ?rescore=1 để chấm lại tất cả (kể cả đã có điểm).
     */
    public function bulkScore(Request $request, JobPost $jobPost): JsonResponse
    {
        $user = $request->user();
        $jobPost->loadMissing('user');

        if (!Gate::forUser($user)->allows('viewApplications', $jobPost)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập tin tuyển dụng này.',
            ], 403);
        }

        $rescoreAll = $request->boolean('rescore');

        $query = JobApplication::where('job_post_id', $jobPost->id);
        if (!$rescoreAll) {
            $query->whereNull('ai_score');
        }

        $applications = $query->orderBy('id')->get();
        $total = $applications->count();

        if ($total === 0) {
            return response()->json([
                'success' => true,
                'total'   => 0,
                'scored'  => 0,
                'failed'  => 0,
                'message' => $rescoreAll
                    ? 'Không có ứng viên nào để chấm lại.'
                    : 'Tất cả ứng viên đã được chấm điểm AI.',
            ]);
        }

        $scored = 0;
        $failed = 0;
        $results = [];

        foreach ($applications as $application) {
            try {
                $result = $this->scorer->scoreAndStore($application);
                $scored++;
                $results[] = [
                    'application_id' => $application->id,
                    'score'          => $result['score'],
                    'summary'        => $result['summary'],
                ];
            } catch (\Throwable $e) {
                $failed++;
                Log::error('AiScoreController: bulk score failed for application', [
                    'application_id' => $application->id,
                    'error'          => $e->getMessage(),
                ]);
                $results[] = [
                    'application_id' => $application->id,
                    'error'          => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success'  => true,
            'total'    => $total,
            'scored'   => $scored,
            'failed'   => $failed,
            'results'  => $results,
            'redirect' => route('hr.job-posts.applications', [
                'jobPost' => $jobPost->id,
                'sort'    => 'ai',
            ]),
        ]);
    }

    /**
     * POST hr/applications/{application}/rescore
     */
    public function rescore(Request $request, JobApplication $application): JsonResponse
    {
        $user = $request->user();
        $application->loadMissing('jobPost.user');

        if (!Gate::forUser($user)->allows('view', $application)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền chấm lại đơn này.',
            ], 403);
        }

        try {
            $result = $this->scorer->scoreAndStore($application);
            return response()->json([
                'success' => true,
                'score'   => $result['score'],
                'summary' => $result['summary'],
                'breakdown' => $result['breakdown'],
            ]);
        } catch (\Throwable $e) {
            Log::error('AiScoreController: rescore failed', [
                'application_id' => $application->id,
                'error'          => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Chấm điểm thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}