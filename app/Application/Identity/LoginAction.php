<?php

namespace App\Application\Identity;

use App\Adapter\Identity\AuthOutputDTO;
use App\Adapter\Identity\AuthSessionManager;
use App\Domain\Identity\UserId;
use App\Domain\Identity\UserRepositoryInterface;
use App\Domain\Shared\Contracts\LoggerInterface;
use Exception;
use Illuminate\Validation\ValidationException;
use RuntimeException;

readonly class LoginAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AuthSessionManager $authSessionManager,
        private LoggerInterface $logger
    ) {}

    /**
     * @throws ValidationException
     */
    public function __invoke(LoginActionValuesInterface $request): AuthOutputDTO
    {
        $email = $request->email();

        try {
            $this->logger->info('Login attempt started', ['email' => $email]);

            // 認証試行
            $this->authSessionManager->attempt(
                $email,
                $request->password(),
                $request->remember()
            );

            // 認証済みユーザー取得
            $eloquentUser = $this->authSessionManager->user();

            // アクティブ状態確認
            $this->authSessionManager->assertActive($eloquentUser);

            // トークン生成
            [$token, $expiresAt] = $this->authSessionManager->generateToken($eloquentUser);

            // ドメインモデル取得
            $userId = new UserId($eloquentUser->id);
            $domainUser = $this->userRepository->findById($userId);

            if ($domainUser === null) {
                $this->logger->error('Domain user not found after successful authentication', [
                    'email' => $email,
                    'eloquent_user_id' => $eloquentUser->id,
                    'user_ulid' => $eloquentUser->id,
                ]);
                throw new RuntimeException('Domain user not found for authenticated user.');
            }

            $this->logger->info('Login successful', [
                'user_id' => $domainUser->id->toString(),
                'email' => $email,
            ]);

            return AuthOutputDTO::create($domainUser, $token, $expiresAt);

        } catch (ValidationException $e) {
            $this->logger->warning('Login failed - validation error', [
                'email' => $email,
                'errors' => $e->errors(),
            ]);
            throw $e;
        } catch (Exception $e) {
            $this->logger->error('Login failed - unexpected error', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
