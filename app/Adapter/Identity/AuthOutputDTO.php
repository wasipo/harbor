<?php

declare(strict_types=1);

namespace App\Adapter\Identity;

use App\Domain\Identity\User;
use DateTimeInterface;

readonly class AuthOutputDTO
{
    public function __construct(
        public readonly UserOutputDTO $user,
        public readonly string $token,
        public readonly DateTimeInterface $expires_at,
    ) {}

    public static function create(User $user, string $token, DateTimeInterface $expires_at): self
    {
        return new self(
            user: UserOutputDTO::fromDomain($user),
            token: $token,
            expires_at: $expires_at,
        );
    }

    /**
     * @return array{
     *     data: array{
     *         user: array{
     *             id: string,
     *             name: string,
     *             email: string,
     *             is_active: bool,
     *             email_verified_at: string|null,
     *             categoryIds: array<string>,
     *             roleIds: array<string>
     *         },
     *         token: string,
     *         expires_at: string
     *     }
     * }
     */
    public function toArray(): array
    {
        return [
            'data' => [
                'user' => $this->user->toArray(),
                'token' => $this->token,
                'expires_at' => $this->expires_at->format(DateTimeInterface::ATOM),
            ],
        ];
    }
}
