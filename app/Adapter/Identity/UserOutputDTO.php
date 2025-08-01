<?php

declare(strict_types=1);

namespace App\Adapter\Identity;

use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\AccessControl\Role\RoleIdCollection;
use App\Domain\Identity\User;
use DateTimeInterface;

readonly class UserOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public bool $is_active,
        public ?string $email_verified_at,
        public CategoryIdCollection $categories,
        public RoleIdCollection $roles
    ) {}

    public static function fromDomain(User $user): self
    {
        return new self(
            id: $user->id->toString(),
            name: $user->name->value,
            email: $user->email->value,
            is_active: $user->isActive(),
            email_verified_at: $user->emailVerifiedAt->format(DateTimeInterface::ATOM),
            categories: $user->categoryIds,
            roles: $user->roleIds
        );
    }

    /**
     * @param  array<User>  $users
     * @return array<UserOutputDTO>
     */
    public static function fromArray(array $users): array
    {
        return array_map(fn (User $user) => self::fromDomain($user), $users);
    }

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     email: string,
     *     is_active: bool,
     *     email_verified_at: string|null,
     *     categoryIds: array<string>,
     *     roleIds: array<string>
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at,
            'categoryIds' => $this->categories->toStringArray(),
            'roleIds' => $this->roles->toStringArray(),
        ];
    }
}
