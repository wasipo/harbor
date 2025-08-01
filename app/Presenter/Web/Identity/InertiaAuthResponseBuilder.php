<?php

declare(strict_types=1);

namespace App\Presenter\Web\Identity;

use App\Adapter\Identity\AuthOutputDTO;
use App\Adapter\Identity\UserOutputDTO;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InertiaAuthResponseBuilder implements WebAuthResponseBuilderInterface
{
    /**
     * ログインフォームのレスポンスを構築
     */
    public function buildLoginFormResponse(): Response
    {
        return Inertia::render('Login');
    }

    /**
     * ログイン成功時のレスポンスを構築
     */
    public function buildLoginSuccessResponse(AuthOutputDTO $dto): RedirectResponse
    {
        // ウェルカムメッセージをフラッシュ
        session()->flash('success', "Welcome back, {$dto->user->name}!");
        
        // 権限に応じたリダイレクト先を決定
        $redirectPath = $this->determineRedirectPath($dto->user);
        
        return redirect()->intended($redirectPath);
    }

    /**
     * ログアウト成功時のレスポンスを構築
     */
    public function buildLogoutResponse(): RedirectResponse
    {
        // ログアウトメッセージをフラッシュ
        session()->flash('info', 'You have been logged out successfully.');
        
        return redirect('/');
    }

    /**
     * ユーザーの権限に基づいてリダイレクト先を決定
     */
    private function determineRedirectPath(UserOutputDTO $user): string
    {
        // 将来的に権限に基づいた振り分けを実装
        // 例：
        // if (in_array('admin', $user->roleIds)) {
        //     return route('admin.dashboard');
        // }
        
        return route('dashboard');
    }
}