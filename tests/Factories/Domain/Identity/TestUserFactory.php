<?php

namespace Tests\Factories\Domain\Identity;

use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\AccessControl\Role\RoleIdCollection;
use App\Domain\Identity\AccountStatus;
use App\Domain\Identity\Email;
use App\Domain\Identity\Name;
use App\Domain\Identity\User;
use App\Domain\Identity\UserId;
use Carbon\CarbonImmutable;

class TestUserFactory
{
    /**
     * デフォルトのドメインUserを作成
     */
    public static function create(
        ?UserId $id = null,
        ?Name $name = null,
        ?Email $email = null,
        ?AccountStatus $status = null,
        ?CarbonImmutable $emailVerifiedAt = null,
        ?CategoryIdCollection $categoryIds = null,
        ?RoleIdCollection $roleIds = null
    ): User {
        return User::reconstitute(
            id: $id ?? UserId::create(),
            name: $name ?? new Name('Test User'),
            email: $email ?? new Email(fake()->unique()->safeEmail()),
            status: $status ?? AccountStatus::ACTIVE,
            emailVerifiedAt: $emailVerifiedAt ?? CarbonImmutable::now(),
            categoryIds: $categoryIds ?? CategoryIdCollection::empty(),
            roleIds: $roleIds ?? RoleIdCollection::empty()
        );
    }
}
