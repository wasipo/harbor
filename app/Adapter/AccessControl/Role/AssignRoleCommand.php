<?php

namespace App\Adapter\AccessControl\Role;

readonly class AssignRoleCommand
{
    public function __construct(
        public string $userId,  // ULID
        public string $roleId,  // ULID
        public ?string $assignedByUserId = null  // ULID
    ) {}
}
