<?php

declare(strict_types=1);

namespace App\Application\RegisterUser;

use App\Adapter\RegisterUser\CreateUserCommand;
use App\Domain\Identity\Email;
use App\Domain\Identity\Name;
use App\Domain\Identity\User;
use App\Domain\Identity\UserRepositoryInterface;
use App\Domain\Shared\Contracts\LoggerInterface;
use DomainException;
use Exception;

readonly class CreateUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * ユーザーを作成し、パスワードとともに永続化
     *
     * @throws DomainException|Exception
     */
    public function __invoke(CreateUserCommand $command): User
    {
        $this->logger->info('User creation started', ['email' => $command->email]);

        try {
            // メールアドレスの重複チェック
            $email = new Email($command->email);
            if ($this->userRepository->existsByEmail($email)) {
                $this->logger->warning('User creation failed - email already exists', [
                    'email' => $command->email,
                ]);
                throw new DomainException('このメールアドレスは既に使用されています');
            }

            // ドメインモデルの作成
            $user = User::create(
                name: new Name($command->name),
                email: $email
            );

            // パスワードとともに永続化
            $savedUser = $this->userRepository->add($user, $command->password);

            $this->logger->info('User created successfully', [
                'user_id' => $savedUser->id->toString(),
                'email' => $command->email,
            ]);

            return $savedUser;

        } catch (Exception $e) {
            $this->logger->error('User creation failed - unexpected error', [
                'email' => $command->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
