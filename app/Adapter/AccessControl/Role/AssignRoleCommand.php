<?php

namespace App\Adapter\AccessControl\Role;

readonly class AssignRoleCommand
{
    public function __construct(
        public int $userId,
        public string $roleId,  // ULID
        public ?int $assignedByUserId = null
    ) {}
}