<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Adapter\AccessControl\Category\CategoryDataDTO;
use App\Adapter\AccessControl\Permission\PermissionDataDTO;
use App\Adapter\AccessControl\Role\RoleDataDTO;
use App\Adapter\Query\DashboardDataDTO;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserCategory;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

final class DashboardQueryService
{
    /**
     * 現在のユーザー情報を取得してDTOとして返す
     *
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
     * @return array<CategoryDataDTO>
     */
    private function buildCategories(User $user): array
    {
        if ($user->activeCategories->isEmpty()) {
            return [];
        }

        return $user->activeCategories->map(function (UserCategory $category) {
            /** @var UserCategory $category */
            /** @var array<string> $permissionKeys */
            $permissionKeys = $category->permissions->pluck('key')->all();

            return new CategoryDataDTO(
                id: $category->id,
                code: $category->code,
                name: $category->name,
                // @phpstan-ignore-next-line pivot プロパティは activeCategories リレーションで確実に存在
                isPrimary: (bool) ($category->pivot->is_primary ?? false),
                permissionKeys: $permissionKeys,
            );
        })->all();
    }

    /**
     * @return array<RoleDataDTO>
     */
    private function buildRoles(User $user): array
    {
        if ($user->roles->isEmpty()) {
            return [];
        }

        return $user->roles->map(function (Role $role) {
            /** @var Role $role */
            /** @var array<string> $permissionKeys */
            $permissionKeys = $role->permissions->pluck('key')->all();

            return new RoleDataDTO(
                id: $role->id,
                key: $role->name,
                name: $role->display_name,
                // @phpstan-ignore-next-line description プロパティは nullable で定義されている
                description: $role->description ?? null,
                permissionKeys: $permissionKeys,
            );
        })->all();
    }

    /**
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
            /** @var Permission $permission */
            return new PermissionDataDTO(
                id: $permission->id,
                key: $permission->key,
                name: $permission->display_name,
            );
        })->values()->all();
    }
}
