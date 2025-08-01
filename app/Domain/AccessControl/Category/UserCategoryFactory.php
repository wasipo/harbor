<?php

namespace App\Domain\AccessControl\Category;

use App\Domain\AccessControl\Permission\PermissionId;
use App\Domain\AccessControl\Permission\PermissionIdCollection;
use App\Models\UserCategory as EloquentUserCategory;
use DateTimeImmutable;
use Exception;

class UserCategoryFactory
{
    /**
     * Create a UserCategory instance from an Eloquent UserCategory model.
     *
     * @throws Exception
     */
    public static function fromEloquent(EloquentUserCategory $eloquentCategory): UserCategory
    {
        // Load permissions and convert to PermissionIdCollection
        $permissionIds = $eloquentCategory->permissions->map(
            fn ($permission) => PermissionId::fromString($permission->id)
        )->all();

        return UserCategory::reconstitute(
            id: UserCategoryId::fromString($eloquentCategory->id),
            code: $eloquentCategory->code,
            name: $eloquentCategory->name,
            description: $eloquentCategory->description,
            isActive: $eloquentCategory->is_active,
            permissionIds: new PermissionIdCollection($permissionIds)
        );
    }

    /**
     * Create a UserCategory instance from an array of data.
     *
     * @param array{
     *     id: string,
     *     code: string,
     *     name: string,
     *     description: string|null,
     *     is_active: bool,
     *     permission_ids?: array<string>
     * } $data
     * @throws Exception
     */
    public static function fromArray(array $data): UserCategory
    {
        $permissionIds = isset($data['permission_ids']) 
            ? PermissionIdCollection::fromStrings($data['permission_ids'])
            : PermissionIdCollection::empty();

        return UserCategory::reconstitute(
            id: UserCategoryId::fromString($data['id']),
            code: $data['code'],
            name: $data['name'],
            description: $data['description'],
            isActive: $data['is_active'],
            permissionIds: $permissionIds
        );
    }
}
