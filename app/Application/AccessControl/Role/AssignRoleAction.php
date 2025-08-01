<?php

namespace App\Application\AccessControl\Role;

use App\Adapter\AccessControl\Role\AssignRoleCommand;
use App\Domain\AccessControl\Role\RoleAssignmentService;
use App\Domain\AccessControl\Role\RoleRepositoryInterface;
use App\Domain\AccessControl\Role\RoleId;
use App\Domain\Identity\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AssignRoleAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly RoleAssignmentService $roleAssignmentService
    ) {}

    public function __invoke(AssignRoleCommand $command): void
    {
        DB::transaction(function () use ($command) {
            // ユーザー取得
            $user = $this->userRepository->findById($command->userId);
            if ($user === null) {
                throw new \DomainException('User not found');
            }

            // ロール取得
            $role = $this->roleRepository->findById(RoleId::fromString($command->roleId));
            if ($role === null) {
                throw new \DomainException('Role not found');
            }

            // 割り当て実行者取得（オプション）
            $assignedBy = null;
            if ($command->assignedByUserId !== null) {
                $assignedBy = $this->userRepository->findById($command->assignedByUserId);
            }

            // ドメインサービスでロール割り当て
            $this->roleAssignmentService->assignRole($user, $role, $assignedBy);
        });
    }
}