<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('plan')->withCount('cvs')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('plan')) {
            if ($request->plan === 'none') {
                $query->whereNull('plan_id');
            } else {
                $query->where('plan_id', $request->plan);
            }
        }

        if ($request->filled('verified')) {
            $query->whereNotNull('email_verified_at', $request->verified === 'yes' ? 'and' : null)
                  ->when($request->verified === 'yes', fn($q) => $q->whereNotNull('email_verified_at'))
                  ->when($request->verified === 'no',  fn($q) => $q->whereNull('email_verified_at'));
        }

        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))   $query->whereDate('created_at', '<=', $request->to);

        $sort = $request->input('sort', 'latest');
        $query = match($sort) {
            'name_asc'    => $query->orderBy('name', 'asc'),
            'name_desc'   => $query->orderBy('name', 'desc'),
            'oldest'      => $query->orderBy('created_at', 'asc'),
            default       => $query->orderBy('created_at', 'desc'),
        };

        $users = $query->paginate(20)->withQueryString();
        $plans = Plan::orderBy('price')->get();

        return view('admin.users.index', compact('users', 'plans'));
    }

    public function show(User $user)
    {
        $user->load(['plan', 'cvs.template', 'payments.plan']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $plans = Plan::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'plans'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'role'    => 'required|in:user,admin,hr',
            'plan_id' => 'nullable|exists:plans,id',
            'plan_expires_at' => 'nullable|date',
        ]);

        $user->update($request->only('name', 'email', 'role', 'plan_id', 'plan_expires_at'));

        return redirect()->route('admin.users.index')->with('success', "Đã cập nhật người dùng {$user->name}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Đã xóa người dùng.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,set_role,set_plan',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:users,id',
            'value'  => 'nullable',
        ]);

        $ids = array_diff($request->ids, [auth()->id()]);

        if (empty($ids)) {
            return back()->with('error', 'Không thể thao tác trên chính tài khoản của bạn.');
        }

        if ($request->action === 'delete') {
            User::whereIn('id', $ids)->delete();
            return back()->with('success', 'Đã xóa ' . count($ids) . ' người dùng.');
        }

        if ($request->action === 'set_role') {
            if (!in_array($request->value, ['user', 'hr', 'admin'])) {
                return back()->with('error', 'Vai trò không hợp lệ.');
            }
            User::whereIn('id', $ids)->update(['role' => $request->value]);
            return back()->with('success', 'Đã đổi vai trò cho ' . count($ids) . ' người dùng.');
        }

        if ($request->action === 'set_plan') {
            $planId = $request->value === '' ? null : (int) $request->value;
            User::whereIn('id', $ids)->update(['plan_id' => $planId]);
            return back()->with('success', 'Đã gán gói cho ' . count($ids) . ' người dùng.');
        }
    }

    public function quickUpdate(Request $request, User $user)
    {
        $request->validate([
            'field' => 'required|in:role,plan_id',
            'value' => 'nullable',
        ]);

        if ($user->id === auth()->id() && $request->field === 'role') {
            return response()->json(['ok' => false, 'msg' => 'Không thể thay đổi vai trò của chính bạn.'], 403);
        }

        if ($request->field === 'role') {
            if (!in_array($request->value, ['user', 'hr', 'admin'])) {
                return response()->json(['ok' => false, 'msg' => 'Vai trò không hợp lệ.'], 422);
            }
            $user->update(['role' => $request->value]);
        } else {
            $user->update(['plan_id' => $request->value === '' ? null : (int) $request->value]);
        }

        return response()->json(['ok' => true]);
    }

    public function export(Request $request)
    {
        $query = User::with('plan');
        if ($request->filled('search')) $query->where(fn($q) => $q->where('name', 'like', '%' . $request->search . '%')->orWhere('email', 'like', '%' . $request->search . '%'));
        if ($request->filled('role')) $query->where('role', $request->role);

        $rows = $query->orderBy('created_at', 'desc')->get();

        $filename = 'users_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($rows) {
            $f = fopen('php://output', 'w');
            fwrite($f, "\xEF\xBB\xBF");
            fputcsv($f, ['ID', 'Tên', 'Email', 'Vai trò', 'Gói', 'Đăng ký', 'Xác minh']);
            foreach ($rows as $u) {
                fputcsv($f, [
                    $u->id,
                    $u->name,
                    $u->email,
                    $u->role,
                    $u->plan->name ?? '',
                    optional($u->created_at)->format('Y-m-d H:i'),
                    $u->email_verified_at ? 'Có' : 'Không',
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
}
