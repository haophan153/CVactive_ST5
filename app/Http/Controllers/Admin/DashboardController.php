<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cv;
use App\Models\Payment;
use App\Models\Template;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'     => User::count(),
            'cvs'       => Cv::count(),
            'templates' => Template::where('is_active', true)->count(),
            'revenue'   => Payment::where('status', 'completed')->sum('amount'),
            'new_users' => User::whereDate('created_at', today())->count(),
            'new_cvs'   => Cv::whereDate('created_at', today())->count(),
        ];

        // CV theo ngày (7 ngày gần nhất)
        $cvsByDay = Cv::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Điền ngày còn thiếu = 0
        $cvsByDay = collect(range(6, 0))->map(function ($daysAgo) use ($cvsByDay) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            $found = $cvsByDay->firstWhere('date', $date);
            return (object)['date' => $date, 'count' => $found ? $found->count : 0];
        });

        // Phân bổ plan
        $planStats = User::select('plans.name', DB::raw('COUNT(users.id) as count'))
            ->leftJoin('plans', 'users.plan_id', '=', 'plans.id')
            ->groupBy('plans.name')
            ->orderByDesc('count')
            ->get();

        $recentUsers    = User::latest()->limit(6)->get();
        $recentPayments = Payment::with(['user', 'plan'])->latest()->limit(6)->get();

        return view('admin.dashboard', compact('stats', 'cvsByDay', 'planStats', 'recentUsers', 'recentPayments'));
    }
}
