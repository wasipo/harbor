<?php

namespace App\Domain\AccessControl\Role;

readonly class AuthorizationRole
{
    public function __construct(
        public int $id,
        public string $name,
        public string $displayName,
        public ?string $description,
        /** @var array<string, array<int, string>> */
        public array $permissions,
    ) {}

    public function hasPermission(string $permission): bool
    {
        if (!isset($this->permissions) || !is_array($this->permissions)) {
            return false;
        }

        [$resource, $action] = explode('.', $permission, 2);

        return isset($this->permissions[$resource]) &&
               in_array($action, $this->permissions[$resource], true);
    }

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }
}
