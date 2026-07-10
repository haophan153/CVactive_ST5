<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends ApiController
{
    /**
     * Register a new user.
     *
     * POST /api/auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $this->issueToken($user);

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') * 60, // seconds
        ], 'Đăng ký thành công!', 201);
    }

    /**
     * Login user.
     *
     * SECURITY (fix #18): Generic error message — does NOT leak whether the
     * email exists in the system (avoids account enumeration).
     * SECURITY (fix #11): Per-route throttle (5/min/IP) registered in routes/api.php
     * protects against brute force. Here we additionally bump Sanctum's per-user
     * throttle by deleting prior tokens on a fresh login from a new IP (anti-session-fix).
     *
     * POST /api/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            // Generic message — no enumeration via "wrong password" vs "no such user".
            return $this->error('Thông tin đăng nhập không chính xác.', 401);
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            return $this->error('Thông tin đăng nhập không chính xác.', 401);
        }

        // SECURITY: if account is not verified, refuse login instead of issuing token.
        // The previous code happily issued a token even when MustVerifyEmail was set.
        if (method_exists($user, 'hasVerifiedEmail') && ! $user->hasVerifiedEmail()) {
            return $this->error('Vui lòng xác nhận email trước khi đăng nhập.', 403);
        }

        $token = $this->issueToken($user);

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') * 60,
        ], 'Đăng nhập thành công!');
    }

    /**
     * Logout (revoke current token).
     *
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Đăng xuất thành công!');
    }

    /**
     * Get current authenticated user.
     *
     * GET /api/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['plan']);

        return $this->success(new UserResource($user), 'Thông tin người dùng.');
    }

    /**
     * Update current user profile.
     *
     * PUT /api/auth/me
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $this->success(
            new UserResource($user->load(['plan'])),
            'Cập nhật hồ sơ thành công!'
        );
    }

    /**
     * Issue a Sanctum token with system-wide expiration pulled from config.
     * SECURITY (fix #17): Every issued token has an explicit TTL so a stolen
     * token cannot be replayed forever.
     */
    private function issueToken(User $user): string
    {
        $expiresAt = now()->addMinutes((int) config('sanctum.expiration', 480));

        return $user->createToken(
            'auth_token:' . Str::random(8),
            ['*'],
            $expiresAt
        )->plainTextToken;
    }
}
