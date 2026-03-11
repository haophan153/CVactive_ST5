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
        $query = User::with('plan')->latest();

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

        $users = $query->paginate(20)->withQueryString();
        $plans = Plan::where('is_active', true)->get();

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
}
