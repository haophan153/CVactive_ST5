<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * L-10: regenerate session ID trước khi gọi authenticate() — tránh
     * session fixation nếu attacker pre-seed session ID trước đó.
     * Trước đây: regenerate SAU khi Auth::attempt succeed — nếu attempt
     * throws exception, session vẫn giữ attacker ID.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // L-10: regenerate trước auth attempt
        $request->session()->regenerate();

        $request->authenticate();

        // L-10: regenerate lại lần nữa sau auth — defense-in-depth
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
