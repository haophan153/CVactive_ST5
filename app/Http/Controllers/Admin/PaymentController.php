<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'plan'])->latest();

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%'));
        }

        if ($request->filled('status')) $query->where('status', $request->input('status'));
        if ($request->filled('plan'))   $query->where('plan_id', $request->input('plan'));
        if ($request->filled('method')) $query->where('payment_method', $request->input('method'));
        if ($request->filled('from'))   $query->whereDate('created_at', '>=', $request->input('from'));
        if ($request->filled('to'))     $query->whereDate('created_at', '<=', $request->input('to'));

        $payments = $query->paginate(20)->withQueryString();
        $plans    = Plan::orderBy('price')->get();

        $revenueStats = [
            'total'      => Payment::where('status', 'completed')->sum('amount'),
            'today'      => Payment::where('status', 'completed')->whereDate('created_at', today())->sum('amount'),
            'this_month' => Payment::where('status', 'completed')->whereMonth('created_at', now()->month)->sum('amount'),
            'count'      => Payment::where('status', 'completed')->count(),
        ];

        $totalAttempts = Payment::count();
        $completedCount = (int) $revenueStats['count'];
        $revenueStats['conversion'] = $totalAttempts > 0 ? round($completedCount / $totalAttempts * 100, 1) : 0;
        $revenueStats['avg']        = $completedCount > 0 ? round($revenueStats['total'] / $completedCount) : 0;

        // Monthly revenue (last 6 months) - kèm highlight current
        $monthlyRevenue = Payment::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyRevenue = collect(range(5, 0))->map(function ($m) use ($monthlyRevenue) {
            $month = now()->subMonths($m)->format('Y-m');
            $found = $monthlyRevenue->firstWhere('month', $month);
            return (object)[
                'month' => $month,
                'total' => $found ? (float) $found->total : 0,
                'count' => $found ? (int) $found->count : 0,
            ];
        });

        // Top plans
        $topPlans = Payment::select('plan_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as revenue'))
            ->where('status', 'completed')
            ->whereNotNull('plan_id')
            ->groupBy('plan_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('plan:id,name,slug,price')
            ->get();

        return view('admin.payments.index', compact(
            'payments', 'plans', 'revenueStats', 'monthlyRevenue', 'topPlans'
        ));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate(['status' => 'required|in:pending,completed,failed,refunded']);
        $payment->update(['status' => $request->status]);
        return back()->with('success', 'Đã cập nhật trạng thái thanh toán.');
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:payments,id',
        ]);

        Payment::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return back()->with('success', 'Đã cập nhật ' . count($request->ids) . ' giao dịch.');
    }

    public function export(Request $request)
    {
        $query = Payment::with(['user', 'plan'])->latest();
        if ($request->filled('search')) $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('plan'))   $query->where('plan_id', $request->plan);

        $rows = $query->get();

        $filename = 'payments_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($rows) {
            $f = fopen('php://output', 'w');
            fwrite($f, "\xEF\xBB\xBF");
            fputcsv($f, ['ID', 'Ngày', 'User', 'Email', 'Gói', 'Phương thức', 'Số tiền', 'Trạng thái', 'Mã GD']);
            foreach ($rows as $p) {
                fputcsv($f, [
                    $p->id,
                    optional($p->created_at)->format('Y-m-d H:i'),
                    optional($p->user)->name,
                    optional($p->user)->email,
                    optional($p->plan)->name,
                    $p->payment_method,
                    $p->amount,
                    $p->status,
                    $p->transaction_id,
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
}
