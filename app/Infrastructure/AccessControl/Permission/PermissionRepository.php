<?php

namespace App\Infrastructure\AccessControl\Permission;

use App\Domain\AccessControl\Permission\Permission;
use App\Domain\AccessControl\Permission\PermissionFactory;
use App\Domain\AccessControl\Permission\PermissionId;
use App\Domain\AccessControl\Permission\PermissionRepositoryInterface;
use App\Models\Permission as EloquentPermission;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function findById(PermissionId $id): ?Permission
    {
        $eloquentPermission = EloquentPermission::where('id', $id->toString())->first();
        
        if ($eloquentPermission === null) {
            return null;
        }

        return PermissionFactory::fromEloquent($eloquentPermission);
    }

    public function findByKey(string $key): ?Permission
    {
        $eloquentPermission = EloquentPermission::where('key', $key)->first();
        
        if ($eloquentPermission === null) {
            return null;
        }

        return PermissionFactory::fromEloquent($eloquentPermission);
    }

    public function findByKeys(array $keys): array
    {
        if (empty($keys)) {
            return [];
        }

        $eloquentPermissions = EloquentPermission::whereIn('key', $keys)->get();
        
        return $eloquentPermissions->map(
            fn($eloquentPermission) => PermissionFactory::fromEloquent($eloquentPermission)
        )->toArray();
    }

    public function findByResource(string $resource): array
    {
        $eloquentPermissions = EloquentPermission::where('resource', $resource)->get();
        
        return $eloquentPermissions->map(
            fn($eloquentPermission) => PermissionFactory::fromEloquent($eloquentPermission)
        )->toArray();
    }

    public function create(Permission $permission): Permission
    {
        $eloquentPermission = EloquentPermission::create([
            'id' => $permission->id->toString(),
            'key' => $permission->key->value,
            'resource' => $permission->resource,
            'action' => $permission->action,
            'display_name' => $permission->name->value,
            'description' => $permission->description,
        ]);

        return PermissionFactory::fromEloquent($eloquentPermission);
    }

    public function all(): array
    {
        $eloquentPermissions = EloquentPermission::all();
        
        return $eloquentPermissions->map(
            fn($eloquentPermission) => PermissionFactory::fromEloquent($eloquentPermission)
        )->toArray();
    }

    public function existsByKey(string $key): bool
    {
        return EloquentPermission::where('key', $key)->exists();
    }
}