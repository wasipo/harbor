<?php

namespace App\Domain\AccessControl\Role;

use App\Domain\Identity\User;
use App\Models\User as EloquentUser;
use App\Models\Role as EloquentRole;
use App\Models\UserRole;
use Carbon\CarbonImmutable;
use DomainException;
use RuntimeException;

/**
 * ロール割り当てドメインサービス
 * ロール割り当ての複雑なビジネスロジックを集約
 */
class RoleAssignmentService
{
    /**
     * ユーザーにロールを割り当てる
     */
    public function assignRole(
        User $user,
        Role $role,
        ?User $assignedBy = null
    ): void {
        // ビジネスルール: 既に同じロールを持っている場合はスキップ
        if ($this->userHasRole($user, $role) === true) {
            return;
        }

        // ビジネスルール: 非アクティブユーザーにはロール割り当て不可
        if ($user->isActive() === false) {
            throw new DomainException('Cannot assign role to inactive user');
        }

        // EloquentモデルのIDを取得
        $eloquentUser = EloquentUser::where('ulid', $user->id->toString())->first();
        $eloquentRole = EloquentRole::where('ulid', $role->id->toString())->first();
        
        if ($eloquentUser === null || $eloquentRole === null) {
            throw new RuntimeException('User or Role not found in database');
        }
        
        $assignedById = null;
        if ($assignedBy !== null) {
            $eloquentAssignedBy = EloquentUser::where('ulid', $assignedBy->id->toString())->first();
            $assignedById = $eloquentAssignedBy?->id;
        }
        
        // 割り当て実行
        UserRole::create([
            'user_id' => $eloquentUser->id,
            'role_id' => $eloquentRole->id,
            'assigned_at' => CarbonImmutable::now(),
            'assigned_by' => $assignedById,
        ]);
    }

    /**
     * ユーザーからロールを剥奪する
     */
    public function revokeRole(
        User $user,
        Role $role
    ): void {
        $eloquentUser = EloquentUser::where('ulid', $user->id->toString())->first();
        $eloquentRole = EloquentRole::where('ulid', $role->id->toString())->first();
        
        if ($eloquentUser !== null && $eloquentRole !== null) {
            UserRole::where('user_id', $eloquentUser->id)
                ->where('role_id', $eloquentRole->id)
                ->delete();
        }
    }

    /**
     * ユーザーのすべてのロールを剥奪する
     */
    public function revokeAllRoles(User $user): void
    {
        $eloquentUser = EloquentUser::where('ulid', $user->id->toString())->first();
        
        if ($eloquentUser !== null) {
            UserRole::where('user_id', $eloquentUser->id)->delete();
        }
    }

    /**
     * ユーザーが特定のロールを持っているか確認
     */
    private function userHasRole(User $user, Role $role): bool
    {
        $eloquentUser = EloquentUser::where('ulid', $user->id->toString())->first();
        $eloquentRole = EloquentRole::where('ulid', $role->id->toString())->first();
        
        if ($eloquentUser === null || $eloquentRole === null) {
            return false;
        }
        
        return UserRole::where('user_id', $eloquentUser->id)
            ->where('role_id', $eloquentRole->id)
            ->exists();
    }
}