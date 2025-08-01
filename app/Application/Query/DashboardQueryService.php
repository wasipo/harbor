<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Adapter\AccessControl\Category\CategoryDataDTO;
use App\Adapter\AccessControl\Permission\PermissionDataDTO;
use App\Adapter\AccessControl\Role\RoleDataDTO;
use App\Adapter\Query\DashboardDataDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

final class DashboardQueryService
{
    /**
     * 現在のユーザー情報を取得してDTOとして返す
     *
     * @return DashboardDataDTO
     * @throws RuntimeException
     */
    public function getDashboardData(): DashboardDataDTO
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            throw new RuntimeException('User not authenticated');
        }

        // 必要なリレーションをロード
        $user->load(['activeCategories.permissions', 'roles.permissions']);

        return new DashboardDataDTO(
            id: $user->id,  // ulid -> id に修正
            name: $user->name,
            email: $user->email,
            categories: $this->buildCategories($user),
            roles: $this->buildRoles($user),
            permissions: $this->buildPermissions($user),
        );
    }

    /**
     * @param User $user
     * @return array<CategoryDataDTO>
     */
    private function buildCategories(User $user): array
    {
        if (!$user->activeCategories) {
            return [];
        }

        return $user->activeCategories->map(function ($category) {
            return new CategoryDataDTO(
                id: $category->id,  // ulid -> id に修正
                code: $category->code,
                name: $category->name,
                isPrimary: (bool) ($category->pivot->is_primary ?? false),
                permissionKeys: $category->permissions->pluck('key')->all(),
            );
        })->all();
    }

    /**
     * @param User $user
     * @return array<RoleDataDTO>
     */
    private function buildRoles(User $user): array
    {
        if (!$user->roles) {
            return [];
        }

        return $user->roles->map(function ($role) {
            return new RoleDataDTO(
                id: $role->id,  // ulid -> id に修正
                key: $role->name,
                name: $role->display_name,
                description: $role->description ?? null,
                permissionKeys: $role->permissions->pluck('key')->all(),
            );
        })->all();
    }

    /**
     * @param User $user
     * @return array<PermissionDataDTO>
     */
    private function buildPermissions(User $user): array
    {
        $permissions = collect();
        
        // ロール経由で権限を取得
        foreach ($user->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }
        
        // カテゴリ経由で権限を取得
        foreach ($user->activeCategories as $category) {
            $permissions = $permissions->merge($category->permissions);
        }
        
        // 重複を除去して返す
        return $permissions->unique('id')->map(function ($permission) {
            return new PermissionDataDTO(
                id: $permission->id,  // ulid -> id に修正
                key: $permission->key,
                name: $permission->display_name,
            );
        })->values()->all();
    }
}