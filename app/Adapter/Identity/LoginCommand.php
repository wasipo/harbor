<?php

declare(strict_types=1);

namespace App\Adapter\Identity;

use App\Application\Identity\LoginActionValuesInterface;

readonly class LoginCommand implements LoginActionValuesInterface
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            email: $validated['email'],
            password: $validated['password'],
            remember: $validated['remember'] ?? false
        );
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function remember(): bool
    {
        return $this->remember;
    }
}