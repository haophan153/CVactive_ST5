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
                ->orWhere('email', 'like', '%'.$request->search.'%')
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->where('plan_id', $request->plan);
        }

        $payments = $query->paginate(20)->withQueryString();
        $plans    = Plan::where('is_active', true)->get();

        // Revenue stats
        $revenueStats = [
            'total'     => Payment::where('status', 'completed')->sum('amount'),
            'today'     => Payment::where('status', 'completed')->whereDate('created_at', today())->sum('amount'),
            'this_month' => Payment::where('status', 'completed')->whereMonth('created_at', now()->month)->sum('amount'),
            'count'     => Payment::where('status', 'completed')->count(),
        ];

        // Monthly revenue (last 6 months)
        $monthlyRevenue = Payment::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.payments.index', compact('payments', 'plans', 'revenueStats', 'monthlyRevenue'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate(['status' => 'required|in:pending,completed,failed,refunded']);
        $payment->update(['status' => $request->status]);
        return back()->with('success', 'Đã cập nhật trạng thái thanh toán.');
    }
}
