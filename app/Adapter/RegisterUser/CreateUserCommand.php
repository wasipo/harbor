<?php

declare(strict_types=1);

namespace App\Adapter\RegisterUser;

readonly class CreateUserCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
