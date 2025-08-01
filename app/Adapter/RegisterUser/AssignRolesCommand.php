<?php

declare(strict_types=1);

namespace App\Adapter\RegisterUser;

readonly class AssignRolesCommand
{
    /**
     * @param array<int, string> $roleIds ULID文字列の配列
     */
    public function __construct(
        public array $roleIds
    ) {}
}