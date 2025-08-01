<?php

declare(strict_types=1);

namespace App\Application\RegisterUser;

use App\Adapter\RegisterUser\AssignRolesCommand;
use App\Domain\AccessControl\Role\RoleIdCollection;
use App\Domain\Identity\UserId;
use App\Domain\Identity\UserRepositoryInterface;
use App\Domain\Shared\Contracts\LoggerInterface;

readonly class AssignRolesAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * ユーザーにロールを割り当てる
     */
    public function __invoke(UserId $userId, AssignRolesCommand $command, ?UserId $assignedBy = null): void
    {
        if (empty($command->roleIds)) {
            return;
        }

        $this->logger->info('Assigning roles to user', [
            'user_id' => $userId->toString(),
            'role_count' => count($command->roleIds),
            'assigned_by' => $assignedBy?->toString(),
        ]);

        $roleIds = RoleIdCollection::fromStrings($command->roleIds);

        $this->userRepository->assignRoles($userId, $roleIds, $assignedBy);

        $this->logger->info('Roles assigned successfully', [
            'user_id' => $userId->toString(),
        ]);
    }
}
