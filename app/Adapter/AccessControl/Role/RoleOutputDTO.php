<?php

declare(strict_types=1);

namespace App\Adapter\AccessControl\Role;

use App\Domain\AccessControl\Role\Role;

readonly class RoleOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $display_name,
        /** @var array<string, array<int, string>> */
        public array $permissions,
    ) {}

    public static function fromModel(Role $role): self
    {
        return new self(
            id: $role->id->toString(),
            name: $role->name,
            display_name: $role->displayName,
            permissions: $role->permissions,
        );
    }

    /**
     * @param  array<Role>  $roles
     * @return array<RoleOutputDTO>
     */
    public static function fromArray(array $roles): array
    {
        return array_map(fn (Role $role) => self::fromModel($role), $roles);
    }

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     display_name: string,
     *     permissions: array<string, array<int, string>>
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'permissions' => $this->permissions,
        ];
    }
}
