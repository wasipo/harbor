<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Permission;

use App\Models\Permission as EloquentPermission;

class PermissionFactory
{
    /**
     * Create a Permission instance from an Eloquent Permission model.
     */
    public static function fromEloquent(EloquentPermission $eloquentPermission): Permission
    {
        return Permission::reconstitute(
            id: PermissionId::fromString($eloquentPermission->id),
            key: new PermissionKey($eloquentPermission->key),
            resource: $eloquentPermission->resource,
            action: $eloquentPermission->action,
            name: new PermissionName($eloquentPermission->display_name),
            description: $eloquentPermission->description
        );
    }

    /**
     * Create a Permission instance from an array of data.
     * @param array{
     *     id: string,
     *     key: string,
     *     resource: string,
     *     action: string,
     *     display_name: string,
     *     description?: string|null
     * } $data
     */
    public static function fromArray(array $data): Permission
    {
        return Permission::reconstitute(
            id: PermissionId::fromString($data['id']),
            key: new PermissionKey($data['key']),
            resource: $data['resource'],
            action: $data['action'],
            name: new PermissionName($data['display_name']),
            description: $data['description'] ?? null
        );
    }
}