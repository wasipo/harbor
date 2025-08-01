<?php

namespace App\Application\AccessControl\Role;

use App\Adapter\AccessControl\Role\RevokeRoleCommand;
use App\Domain\AccessControl\Role\RoleAssignmentService;
use App\Domain\AccessControl\Role\RoleId;
use App\Domain\AccessControl\Role\RoleRepositoryInterface;
use App\Domain\Identity\UserId;
use App\Domain\Identity\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class RevokeRoleAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository,
        private RoleAssignmentService $roleAssignmentService
    ) {}

    /**
     * @throws Throwable
     */
    public function __invoke(RevokeRoleCommand $command): void
    {
        DB::transaction(function () use ($command) {

            $userId = new UserId($command->userId);

            // ユーザー取得
            $user = $this->userRepository->findById($userId);
            if ($user === null) {
                throw new \DomainException('User not found');
            }

            // ロール取得
            $role = $this->roleRepository->findById(RoleId::fromString($command->roleId));
            if ($role === null) {
                throw new \DomainException('Role not found');
            }

            // ドメインサービスでロール剥奪
            $this->roleAssignmentService->revokeRole($user, $role);
        });
    }
}
