<?php

namespace App\Adapter\AccessControl\Role;

readonly class RevokeRoleCommand
{
    public function __construct(
        public int $userId,
        public string $roleId  // ULID
    ) {}
}