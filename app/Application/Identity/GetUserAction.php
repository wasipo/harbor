<?php

namespace App\Application\Identity;

use App\Adapter\Identity\UserOutputDTO;
use App\Domain\Identity\UserRepositoryInterface;

readonly class GetUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(int $userId): UserOutputDTO
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            abort(404, 'User not found');
        }

        return UserOutputDTO::fromDomain($user);
    }
}
