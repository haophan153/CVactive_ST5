<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\JobApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Main user dashboard: stats, recent CVs, applications, tips, activity timeline.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // ── Stats (lightweight, single queries) ────────────────────────────
        $cvs = $user->cvs()->with(['template', 'sections', 'shares'])->latest()->get();

        $stats = [
            'total'        => $cvs->count(),
            'completed'    => $cvs->where('is_draft', false)->count(),
            'drafts'       => $cvs->where('is_draft', true)->count(),
            'applications' => JobApplication::whereIn('cv_id', $cvs->pluck('id'))->count(),
            'shared'       => $cvs->filter(fn ($cv) => $cv->shares->isNotEmpty())->count(),
        ];

        // ── Completion rate (0–100) per CV ─────────────────────────────────
        $cvs->each(function ($cv) {
            $cv->completion = $this->completionFor($cv);
            $cv->share_url  = $cv->shares->isNotEmpty()
                ? route('cv.public', $cv->shares->first()->share_token)
                : null;
        });

        // ── 14-day CV creation chart ───────────────────────────────────────
        $start = Carbon::now()->subDays(13)->startOfDay();
        $counts = $user->cvs()
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->where('created_at', '>=', $start)
            ->groupBy('d')
            ->pluck('c', 'd');

        $timeline = collect(range(13, 0))->map(function ($ago) use ($counts) {
            $date = Carbon::now()->subDays($ago)->format('Y-m-d');
            return (object) [
                'date'  => $date,
                'count' => (int) ($counts[$date] ?? 0),
            ];
        });

        // ── Recent applications (last 5) ───────────────────────────────────
        $applications = JobApplication::with(['jobPost'])
            ->whereIn('cv_id', $cvs->pluck('id'))
            ->latest()
            ->limit(5)
            ->get();

        // ── Activity feed (last 10 events on user's CVs) ──────────────────
        $activity = $this->buildActivity($cvs, $applications);

        // ── Pro tips personalized by user state ───────────────────────────
        $tips = $this->personalizedTips($stats, $cvs);

        return view('dashboard', [
            'stats'        => $stats,
            'cvs'          => $cvs,
            'timeline'     => $timeline,
            'applications' => $applications,
            'activity'     => $activity,
            'tips'         => $tips,
            'completion'   => $this->overallCompletion($cvs),
        ]);
    }

    /**
     * Lightweight JSON endpoint polled by the dashboard for realtime updates.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $user = $request->user();

        $cvs = $user->cvs()->with(['template', 'shares'])->latest()->get();

        $stats = [
            'total'        => $cvs->count(),
            'completed'    => $cvs->where('is_draft', false)->count(),
            'drafts'       => $cvs->where('is_draft', true)->count(),
            'shared'       => $cvs->filter(fn ($cv) => $cv->shares->isNotEmpty())->count(),
            'updated_at'   => optional($cvs->max('updated_at'))?->toIso8601String(),
        ];

        return response()->json([
            'stats'      => $stats,
            'server_ts'  => now()->toIso8601String(),
        ])->header('Cache-Control', 'no-store');
    }

    // ── helpers ─────────────────────────────────────────────────────────

    /**
     * Compute a 0–100% completion score for a CV based on its sections.
     */
    private function completionFor(Cv $cv): int
    {
        $info    = $cv->personal_info ?? [];
        $hasName     = !empty($info['full_name']     ?? null);
        $hasEmail    = !empty($info['email']         ?? null);
        $hasPhone    = !empty($info['phone']         ?? null);
        $hasAvatar   = !empty($info['avatar']        ?? null);
        $hasSections = $cv->sections?->count() > 0;

        $filled = (int) $hasName + (int) $hasEmail + (int) $hasPhone + (int) $hasAvatar + (int) $hasSections;
        return min(100, $filled * 20);
    }

    private function overallCompletion($cvs): int
    {
        if ($cvs->isEmpty()) {
            return 0;
        }
        return (int) round($cvs->avg(fn ($cv) => $cv->completion));
    }

    /**
     * Build a normalized activity timeline (CV created, updated, shared, applied).
     */
    private function buildActivity($cvs, $applications): array
    {
        $events = [];

        foreach ($cvs as $cv) {
            $events[] = [
                'type'      => 'cv_created',
                'title'     => $cv->title,
                'when'      => $cv->created_at,
                'icon'      => 'plus',
                'color'     => 'indigo',
                'url'       => route('cv.edit', $cv),
            ];
            if ($cv->updated_at && $cv->updated_at->ne($cv->created_at)) {
                $events[] = [
                    'type'      => 'cv_updated',
                    'title'     => $cv->title,
                    'when'      => $cv->updated_at,
                    'icon'      => 'pencil',
                    'color'     => 'sky',
                    'url'       => route('cv.edit', $cv),
                ];
            }
            foreach ($cv->shares as $share) {
                $events[] = [
                    'type'  => 'cv_shared',
                    'title' => $cv->title,
                    'when'  => $share->created_at,
                    'icon'  => 'link',
                    'color' => 'emerald',
                    'url'   => $cv->share_url,
                ];
            }
        }

        foreach ($applications as $app) {
            $events[] = [
                'type'  => 'application_sent',
                'title' => optional($app->jobPost)->title ?? 'Vị trí đã ứng tuyển',
                'when'  => $app->created_at,
                'icon'  => 'briefcase',
                'color' => 'amber',
                'url'   => optional($app->jobPost) ? route('jobs.show', $app->jobPost) : null,
            ];
        }

        usort($events, fn ($a, $b) => $b['when'] <=> $a['when']);

        return array_slice($events, 0, 10);
    }

    /**
     * Pick 3 tips based on the user's current state.
     */
    private function personalizedTips(array $stats, $cvs): array
    {
        $tips = [];

        if ($stats['total'] === 0) {
            $tips[] = [
                'title' => 'Bắt đầu với template phù hợp',
                'body'  => 'Chọn 1 trong hơn 20 mẫu CV chuyên nghiệp để tạo CV đầu tiên chỉ trong 5 phút.',
                'cta'   => ['label' => 'Khám phá template', 'url' => route('templates.index')],
                'icon'  => 'sparkles',
            ];
        }

        if ($stats['drafts'] > 0) {
            $tips[] = [
                'title' => 'Hoàn thiện CV nháp của bạn',
                'body'  => 'Bạn có ' . $stats['drafts'] . ' CV đang dở dang. Hoàn thành để dễ dàng tải PDF và chia sẻ.',
                'cta'   => ['label' => 'Xem CV nháp', 'url' => route('dashboard') . '#drafts'],
                'icon'  => 'pencil',
            ];
        }

        if ($stats['total'] > 0 && $stats['shared'] === 0) {
            $tips[] = [
                'title' => 'Tạo link chia sẻ công khai',
                'body'  => 'Link chia sẻ giúp bạn gửi CV tới nhà tuyển dụng chỉ với 1 click, không cần tải file.',
                'cta'   => ['label' => 'Bật chia sẻ', 'url' => route('templates.index')],
                'icon'  => 'link',
            ];
        }

        if ($stats['applications'] < 3 && $stats['completed'] > 0) {
            $tips[] = [
                'title' => 'Ứng tuyển ngay để tăng cơ hội',
                'body'  => 'Bạn đã có CV hoàn chỉnh — bắt đầu ứng tuyển từ hàng trăm công việc phù hợp.',
                'cta'   => ['label' => 'Xem việc làm', 'url' => route('jobs.index')],
                'icon'  => 'briefcase',
            ];
        }

        $tips[] = [
            'title' => 'Đặt mục tiêu nghề nghiệp rõ ràng',
            'body'  => 'Một mục tiêu nghề nghiệp dưới 60 từ giúp CV của bạn nổi bật hơn 40% so với trung bình.',
            'cta'   => ['label' => 'Thêm mục tiêu', 'url' => route('templates.index')],
            'icon'  => 'target',
        ];

        return array_slice($tips, 0, 3);
    }
}
