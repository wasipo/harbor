<?php

namespace App\Adapter\Identity;

readonly class CreateUserCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public bool $isActive,
        /** @var array<int, int> */
        public array $categoryIds,
        /** @var array<int, int> */
        public array $roleIds
    ) {}
}
