<?php

declare(strict_types=1);

namespace App\Adapter\Query;

use App\Adapter\AccessControl\Category\CategoryDataDTO;
use App\Adapter\AccessControl\Permission\PermissionDataDTO;
use App\Adapter\AccessControl\Role\RoleDataDTO;

final readonly class DashboardDataDTO
{
    /**
     * @param array<CategoryDataDTO> $categories
     * @param array<RoleDataDTO> $roles
     * @param array<PermissionDataDTO> $permissions
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public array $categories,
        public array $roles,
        public array $permissions,
    ) {}
}