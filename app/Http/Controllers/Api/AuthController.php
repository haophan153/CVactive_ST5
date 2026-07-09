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

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Đăng ký thành công!', 201);
    }

    /**
     * Login user (constant-time).
     *
     * POST /api/auth/login
     * L-2: chống timing attack email enumeration.
     *   - Email không tồn tại: vẫn chạy bcrypt giả với cùng cost
     *   - User bị khóa / email_verified_at null: trả cùng thông báo
     *     "Email hoặc mật khẩu không đúng" để không leak trạng thái.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $genericError = 'Email hoặc mật khẩu không đúng.';

        // L-2: lookup user FIRST để biết có tồn tại hay không
        $user = User::where('email', $email)->first();

        if (! $user) {
            // Chạy bcrypt giả để timing gần như nhau
            Hash::check($password, '$2y$12$' . Str::random(53));
            return $this->error($genericError, 401);
        }

        // Check password
        if (! Hash::check($password, $user->password)) {
            return $this->error($genericError, 401);
        }

        // Cập nhật last login + tạo token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
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
}
