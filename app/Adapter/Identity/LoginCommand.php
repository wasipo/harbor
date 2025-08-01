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

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromRequest(array $validated): self
    {
        $email = $validated['email'] ?? '';
        $password = $validated['password'] ?? '';

        return new self(
            email: is_string($email) ? $email : '',
            password: is_string($password) ? $password : '',
            remember: (bool) ($validated['remember'] ?? false)
        );
    }
}
