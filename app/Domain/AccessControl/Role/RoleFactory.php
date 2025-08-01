<?php

namespace App\Domain\AccessControl\Role;

use App\Domain\AccessControl\Permission\PermissionId;
use App\Domain\AccessControl\Permission\PermissionIdCollection;
use App\Models\Role as EloquentRole;
use Exception;

class RoleFactory
{
    /**
     * Create a Role instance from an Eloquent Role model.
     *
     * @throws Exception
     */
    public static function fromEloquent(EloquentRole $eloquentRole): Role
    {
        // Load permissions and convert to PermissionIdCollection
        $permissionIds = $eloquentRole->permissions->map(
            fn ($permission) => PermissionId::fromString($permission->id)
        )->all();

        return Role::reconstitute(
            id: RoleId::fromString($eloquentRole->id),
            name: $eloquentRole->name,
            displayName: $eloquentRole->display_name,
            permissionIds: new PermissionIdCollection($permissionIds)
        );
    }

    /**
     * Create a Role instance from an array of data.
     *
     * @param array{
     *     id: string,
     *     name: string,
     *     display_name: string,
     *     permission_ids?: array<string>
     * } $data
     * @throws Exception
     */
    public static function fromArray(array $data): Role
    {
        $permissionIds = isset($data['permission_ids']) 
            ? PermissionIdCollection::fromStrings($data['permission_ids'])
            : PermissionIdCollection::empty();

        return Role::reconstitute(
            id: RoleId::fromString($data['id']),
            name: $data['name'],
            displayName: $data['display_name'],
            permissionIds: $permissionIds
        );
    }
}
