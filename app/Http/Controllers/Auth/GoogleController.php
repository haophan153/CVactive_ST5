<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     *
     * Scopes are configured statically in config/services.php to avoid method
     * calls that don't exist on the Socialite Provider contract (v5).
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Google OAuth callback.
     *
     * Flow:
     *  1. Exchange code for user info
     *  2. Validate email exists (Google may withhold it for unverified accounts)
     *  3. Find user by google_id first, then by email
     *  4. If existing email user without google_id → link the account, KEEP their password
     *  5. Login with remember=true and regenerate session
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth callback failed', [
                'error'   => $e->getMessage(),
                'class'   => get_class($e),
                'ip'      => request()->ip(),
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Đăng nhập Google thất bại. Vui lòng thử lại hoặc dùng email/mật khẩu.');
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            Log::warning('Google OAuth returned empty email', [
                'google_id' => $googleUser->getId(),
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Tài khoản Google của bạn chưa cấp quyền email. Vui lòng cho phép truy cập email rồi thử lại.');
        }

        $googleId = $googleUser->getId();
        $name     = $googleUser->getName() ?: 'Google User';
        $avatar   = $googleUser->getAvatar();

        // H3: Google ID là dãy số, validate format để chống inject
        // hoặc giá trị rác (null, array, string dài bất thường).
        // Google ID chính thức là chuỗi số ASCII 21-25 ký tự.
        if (!is_string($googleId) || !preg_match('/^[0-9]{10,30}$/', $googleId)) {
            Log::warning('Google OAuth returned invalid google_id format', [
                'google_id' => is_scalar($googleId) ? (string) $googleId : gettype($googleId),
                'ip'        => request()->ip(),
            ]);
            return redirect()
                ->route('login')
                ->with('error', 'Phản hồi từ Google không hợp lệ. Vui lòng thử lại.');
        }

        // H3: Email format phải hợp lệ — tránh rác vào DB
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            Log::warning('Google OAuth returned invalid email', [
                'email' => is_scalar($email) ? substr((string) $email, 0, 50) : gettype($email),
            ]);
            return redirect()
                ->route('login')
                ->with('error', 'Email từ Google không hợp lệ. Vui lòng thử lại.');
        }

        // H3: name & avatar phải giới hạn độ dài
        $name   = is_string($name) ? mb_substr(trim($name), 0, 255) : 'Google User';
        $avatar = is_string($avatar) && strlen($avatar) < 2048 ? $avatar : null;

        // 1) Existing user linked to this Google account → just refresh avatar/name.
        $user = User::where('google_id', $googleId)->first();

        // 2) Email already exists → link Google to that account, DO NOT overwrite password.
        if (! $user) {
            $existingByEmail = User::where('email', $email)->first();

            if ($existingByEmail) {
                $user = $existingByEmail;
                $user->forceFill([
                    'google_id' => $googleId,
                    'avatar'    => $avatar ?: $user->avatar,
                    'name'      => $user->name ?: $name,
                ])->save();
            }
        }

        // 3) Brand new user → create with random password (they can set one later).
        if (! $user) {
            $user = User::create([
                'name'              => $name,
                'email'             => $email,
                'google_id'         => $googleId,
                'avatar'            => $avatar,
                'password'          => bcrypt(Str::random(32)),
                'email_verified_at' => now(),
                'role'              => 'user',
            ]);
        }

        // 4) Refresh Google avatar on every login:
        //    Download remote image to local storage so the URL is stable and doesn't expire.
        //    On failure we keep whatever avatar the user already had (uploaded or from previous login).
        if ($avatar) {
            $localPath = $this->downloadGoogleAvatar($avatar, $googleId, $user->avatar);
            if ($localPath) {
                $user->forceFill(['avatar' => $localPath])->save();
            }
        }

        // Mark email as verified on every Google login (Google already verified it).
        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, remember: true);
        $request = request();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Download the user's Google avatar to local storage and return the relative path.
     *
     * Why download instead of storing the Google URL?
     *  Google signs avatar URLs with a short-lived token; the link breaks in minutes.
     *  Storing locally guarantees the image keeps working forever.
     *
     * @param  string      $avatarUrl  Remote URL returned by Google.
     * @param  string      $googleId   Stable Google ID used as filename seed.
     * @param  string|null $currentAvatar  Existing avatar value (path or URL) — used to clean up old local files.
     * @return string|null            Relative storage path on success, null on failure.
     */
    private function downloadGoogleAvatar(string $avatarUrl, string $googleId, ?string $currentAvatar): ?string
    {
        try {
            $response = Http::timeout(10)->get($avatarUrl);
        } catch (\Throwable $e) {
            Log::warning('Google avatar download failed (network)', [
                'google_id' => $googleId,
                'error'     => $e->getMessage(),
            ]);
            return null;
        }

        if (! $response->ok()) {
            Log::warning('Google avatar download failed (status)', [
                'google_id' => $googleId,
                'status'    => $response->status(),
            ]);
            return null;
        }

        $body   = $response->body();
        $ext    = $this->guessImageExtension($response->header('Content-Type'));
        $folder = 'avatars';

        // Delete previous locally-stored avatar (uploaded by user, or a stale Google download)
        // but NEVER delete a URL — those are remote and might still be in use.
        if ($currentAvatar && ! str_starts_with($currentAvatar, 'http')) {
            Storage::disk('public')->delete($currentAvatar);
        }

        $path = $folder . '/google-' . $googleId . '.' . $ext;
        Storage::disk('public')->put($path, $body);

        return $path;
    }

    /**
     * Best-effort extension guess from a Content-Type header.
     * Defaults to "jpg" since Google almost always returns JPEG.
     */
    private function guessImageExtension(?string $contentType): string
    {
        return match (true) {
            $contentType && str_contains($contentType, 'png')  => 'png',
            $contentType && str_contains($contentType, 'webp') => 'webp',
            $contentType && str_contains($contentType, 'gif')  => 'gif',
            default                                            => 'jpg',
        };
    }
}