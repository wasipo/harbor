<?php

declare(strict_types=1);

namespace App\Presenter\Web\Identity;

use App\Adapter\Identity\AuthOutputDTO;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

interface WebAuthResponseBuilderInterface
{
    /**
     * ログインフォームのレスポンスを構築
     */
    public function buildLoginFormResponse(): Response;

    /**
     * ログイン成功時のレスポンスを構築
     */
    public function buildLoginSuccessResponse(AuthOutputDTO $dto): RedirectResponse;

    /**
     * ログアウト成功時のレスポンスを構築
     */
    public function buildLogoutResponse(): RedirectResponse;
}