<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Services;

use App\Domain\AccessControl\Category\UserCategory;
use App\Domain\AccessControl\Permission\PermissionCollection;
use App\Domain\AccessControl\Role\Role;
use App\Models\Permission;
use App\Models\Role as EloquentRole;
use App\Models\UserCategory as EloquentUserCategory;

/**
 * Permission Assignment Service
 * 
 * 権限割り当ての管理を行うドメインサービス
 */
class PermissionAssignmentService
{
    /**
     * ロールに権限を一括で割り当てる（同期）
     */
    public function syncRolePermissions(Role $role, PermissionCollection $permissions): void
    {
        $eloquentRole = EloquentRole::where('ulid', $role->id->toString())->firstOrFail();
        
        // PermissionCollectionからULID配列を取得してEloquentのIDを一括取得
        $ulids = $permissions->toIds()->toStringArray();
        $permissionIds = Permission::whereIn('ulid', $ulids)->pluck('id')->all();
        
        // sync メソッドで権限を同期
        $eloquentRole->permissions()->sync($permissionIds);
    }

    /**
     * カテゴリに権限を一括で割り当てる（同期）
     */
    public function syncCategoryPermissions(UserCategory $category, PermissionCollection $permissions): void
    {
        $eloquentCategory = EloquentUserCategory::find($category->id);
        
        // PermissionCollectionからULID配列を取得してEloquentのIDを一括取得
        $ulids = $permissions->toIds()->toStringArray();
        $permissionIds = Permission::whereIn('ulid', $ulids)->pluck('id')->all();
        
        // sync メソッドで権限を同期
        $eloquentCategory->permissions()->sync($permissionIds);
    }
}