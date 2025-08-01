<?php

declare(strict_types=1);

namespace App\Adapter\AccessControl\Role;

final readonly class RoleDataDTO
{
    /**
     * @param array<string> $permissionKeys
     */
    public function __construct(
        public string $id,
        public string $key,
        public string $name,
        public ?string $description,
        public array $permissionKeys = [],
    ) {}
}