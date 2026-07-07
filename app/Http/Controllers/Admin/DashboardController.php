<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cv;
use App\Models\Payment;
use App\Models\Template;
use App\Models\BlogPost;
use App\Models\Plan;
use App\Models\JobPost;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $lastMonth = now()->subMonth();

        $stats = [
            'users'      => User::count(),
            'cvs'        => Cv::count(),
            'templates'  => Template::where('is_active', true)->count(),
            'revenue'    => (float) Payment::where('status', 'completed')->sum('amount'),
            'job_posts'  => JobPost::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        $trends = [
            'users'     => $this->trend(User::class, 'created_at'),
            'cvs'       => $this->trend(Cv::class, 'created_at'),
            'revenue'   => $this->trend(Payment::class, 'created_at', fn($q) => $q->where('status', 'completed')),
            'job_posts' => $this->trend(JobPost::class, 'created_at'),
        ];

        // Revenue 6 tháng
        $revenueByMonth = Payment::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $revenueByMonth = collect(range(5, 0))->map(function ($m) use ($revenueByMonth) {
            $month = now()->subMonths($m)->format('Y-m');
            $found = $revenueByMonth->firstWhere('month', $month);
            return (object)['month' => $month, 'total' => $found ? (float) $found->total : 0];
        });

        // User growth 30 ngày
        $usersByDay = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $usersGrowth = collect(range(29, 0))->map(function ($d) use ($usersByDay) {
            $date = now()->subDays($d)->format('Y-m-d');
            return (object)[
                'date'  => $date,
                'count' => isset($usersByDay[$date]) ? (int) $usersByDay[$date]->count : 0,
            ];
        });

        // Phân bổ plan
        $planStats = User::select('plans.name', 'plans.id', DB::raw('COUNT(users.id) as count'))
            ->leftJoin('plans', 'users.plan_id', '=', 'plans.id')
            ->groupBy('plans.id', 'plans.name')
            ->orderByDesc('count')
            ->get();

        // Top 5 HR (đăng tuyển nhiều nhất)
        $topHr = JobPost::select('user_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('user_id')
            ->where('user_id', '>', 0)
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user:id,name,email')
            ->limit(5)
            ->get()
            ->filter(fn ($x) => $x->user !== null)
            ->values();

        // Recent activity timeline (10 mới nhất, gộp users + payments + posts)
        $recentUsers    = User::latest()->limit(4)->get();
        $recentPayments = Payment::with(['user', 'plan'])->latest()->limit(3)->get();
        $recentPosts    = BlogPost::with('author')->latest()->limit(3)->get();

        $activity = collect()
            ->merge($recentUsers->map(fn ($u) => [
                'type'  => 'user', 'icon' => 'user', 'color' => 'indigo',
                'title' => "Người dùng mới: {$u->name}",
                'sub'   => $u->email,
                'time'  => $u->created_at,
            ]))
            ->merge($recentPayments->map(fn ($p) => [
                'type'  => 'payment', 'icon' => 'cash', 'color' => 'emerald',
                'title' => "Thanh toán " . number_format($p->amount, 0, ',', '.') . '₫ từ ' . ($p->user->name ?? 'ẩn'),
                'sub'   => $p->plan->name ?? '',
                'time'  => $p->created_at,
            ]))
            ->merge($recentPosts->map(fn ($p) => [
                'type'  => 'post', 'icon' => 'edit', 'color' => 'rose',
                'title' => $p->status === 'published' ? "Đăng bài: {$p->title}" : "Nháp mới: {$p->title}",
                'sub'   => $p->author->name ?? '',
                'time'  => $p->created_at,
            ]))
            ->sortByDesc('time')
            ->take(10)
            ->values();

        // System health
        $systemHealth = [
            'disk_free'    => @disk_free_space(base_path()) ?: 0,
            'disk_total'   => @disk_total_space(base_path()) ?: 1,
            'cache_size'   => $this->dirSize(storage_path('framework/cache/data')),
            'logs_size'    => $this->dirSize(storage_path('logs')),
            'pending_jobs' => DB::table('jobs')->count(),
        ];

        $unreadContacts = Contact::where('is_read', false)->count();

        return view('admin.dashboard', compact(
            'stats', 'trends',
            'revenueByMonth', 'usersGrowth', 'planStats',
            'topHr', 'activity', 'systemHealth', 'unreadContacts'
        ));
    }

    private function trend(string $model, string $column, ?\Closure $extra = null): array
    {
        $last30 = now()->subDays(30);
        $prev30 = now()->subDays(60);
        $now30  = now();

        $current = $this->countBetween($model, $column, $last30, $now30, $extra);
        $previous = $this->countBetween($model, $column, $prev30, $last30, $extra);

        if ($previous == 0) {
            return ['pct' => $current > 0 ? 100 : 0, 'dir' => $current > 0 ? 'up' : 'flat'];
        }

        $pct = round((($current - $previous) / $previous) * 100);
        $dir = $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat');
        return ['pct' => abs($pct), 'dir' => $dir];
    }

    private function countBetween(string $model, string $column, $from, $to, ?\Closure $extra = null): int
    {
        $q = $model::query()->whereBetween($column, [$from, $to]);
        if ($extra) $q = $extra($q);
        return $q->count();
    }

    private function dirSize(string $path): int
    {
        if (!is_dir($path)) return 0;
        $size = 0;
        try {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $f) {
                $size += $f->getSize();
            }
        } catch (\Throwable $e) {}
        return $size;
    }
}
