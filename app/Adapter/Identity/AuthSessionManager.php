<?php

declare(strict_types=1);

namespace App\Adapter\Identity;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use RuntimeException;

final class AuthSessionManager
{
    /**
     * @throws ValidationException
     */
    public function attempt(string $email, string $password, bool $remember = false): void
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }
    }

    public function user(): User
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            throw new RuntimeException('Authenticated user is not valid.');
        }

        return $user;
    }

    /**
     * @throws ValidationException
     */
    public function assertActive(User $user): void
    {
        if (!$user->isActive()) {
            // 非アクティブユーザーはログアウトさせる
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => ['アカウントが無効化されています。'],
            ]);
        }
    }

    /**
     * 認証トークン生成（＋有効期限を返す）
     *
     * @return array{0: string, 1: Carbon}
     */
    public function generateToken(User $user): array
    {
        $token = $user->createToken('api-token')->plainTextToken;
        $expiresAt = now()->addDays(7);

        return [$token, $expiresAt];
    }

    /**
     * ログアウト処理
     * Web/API両方に対応
     */
    public function logout(?User $user = null): void
    {
        $user = $user ?? Auth::user();

        // APIトークンがあればAPI認証のログアウト
        if ($user instanceof User && $this->hasApiToken($user)) {
            $this->revokeApiToken($user);

            return;
        }

        // それ以外はWebセッションのログアウト
        $this->logoutWebSession();
    }

    /**
     * ユーザーがAPIトークンを持っているかチェック
     */
    private function hasApiToken(User $user): bool
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        return $token !== null;
    }

    /**
     * APIトークンを無効化
     */
    private function revokeApiToken(User $user): void
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();
        $token?->delete();
    }

    /**
     * Webセッションのログアウト
     */
    private function logoutWebSession(): void
    {
        Auth::logout();
    }
}
