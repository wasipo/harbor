<?php

declare(strict_types=1);

namespace App\Application\Identity;

use App\Adapter\Identity\AuthSessionManager;
use App\Domain\Shared\Contracts\LoggerInterface;
use Exception;

readonly class LogoutAction
{
    public function __construct(
        private AuthSessionManager $authSessionManager,
        private LoggerInterface $logger
    ) {}

    public function __invoke(): void
    {
        try {
            // 現在のユーザー情報を取得
            $user = $this->authSessionManager->user();
            $userId = $user->id;

            $this->logger->info('Logout attempt started', ['user_id' => $userId]);

            // ログアウト処理
            $this->authSessionManager->logout($user);

            $this->logger->info('Logout successful', ['user_id' => $userId]);

        } catch (Exception $e) {
            // ログアウトは基本的に失敗しないが、念のため
            $this->logger->error('Logout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
