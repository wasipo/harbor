<?php

namespace App\Adapter\AccessControl\Role;

readonly class RevokeRoleCommand
{
    public function __construct(
        public string $userId,  // ULID
        public string $roleId  // ULID
    ) {}
}
