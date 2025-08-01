<?php

namespace App\Domain\AccessControl\Permission;

interface PermissionRepositoryInterface
{
    /**
     * Find a permission by its ID
     */
    public function findById(PermissionId $id): ?Permission;

    /**
     * Find a permission by its key (e.g., "user.read")
     */
    public function findByKey(string $key): ?Permission;

    /**
     * Find multiple permissions by their keys
     *
     * @param  array<int, string>  $keys
     * @return array<int, Permission>
     */
    public function findByKeys(array $keys): array;

    /**
     * Find all permissions for a specific resource
     *
     * @return array<int, Permission>
     */
    public function findByResource(string $resource): array;

    /**
     * Create a new permission
     */
    public function create(Permission $permission): Permission;

    /**
     * Get all permissions
     *
     * @return array<int, Permission>
     */
    public function all(): array;

    /**
     * Check if a permission key exists
     */
    public function existsByKey(string $key): bool;
}
