<?php

namespace App\Domain\Identity;

use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\AccessControl\Role\RoleIdCollection;
use App\Models\User as EloquentUser;
use Carbon\CarbonImmutable;

class UserFactory
{
    /**
     * Create a User instance from an Eloquent User model.
     */
    public static function fromEloquent(EloquentUser $eloquentUser): User
    {
        // AccountStatusの決定
        $status = $eloquentUser->is_active ? AccountStatus::ACTIVE : AccountStatus::INACTIVE;

        // Category IDsとRole IDsを抽出
        $categoryIds = self::extractCategoryIds($eloquentUser);
        $roleIds = self::extractRoleIds($eloquentUser);

        return User::reconstitute(
            id: UserId::fromString($eloquentUser->id),
            name: new Name($eloquentUser->name),
            email: new Email($eloquentUser->email),
            status: $status,
            emailVerifiedAt: $eloquentUser->email_verified_at,
            categoryIds: CategoryIdCollection::fromStrings($categoryIds),
            roleIds: RoleIdCollection::fromStrings($roleIds)
        );
    }

    /**
     * Create a User instance from an array of data.
     * @param array{
     *     id: string,
     *     name: string,
     *     email: string,
     *     is_active: bool,
     *     category_ids?: array<int, string>,
     *     role_ids?: array<int, string>
     * } $data
     */
    public static function fromArray(array $data): User
    {
        $status = $data['is_active'] ? AccountStatus::ACTIVE : AccountStatus::INACTIVE;

        return User::reconstitute(
            id: UserId::fromString($data['id']),
            name: new Name($data['name']),
            email: new Email($data['email']),
            status: $status,
            emailVerifiedAt: isset($data['email_verified_at']) ? CarbonImmutable::parse($data['email_verified_at']) : null,
            categoryIds: CategoryIdCollection::fromStrings($data['category_ids'] ?? []),
            roleIds: RoleIdCollection::fromStrings($data['role_ids'] ?? [])
        );
    }

    /**
     * @return array<int, string>
     */
    private static function extractCategoryIds(EloquentUser $eloquentUser): array
    {
        if (!$eloquentUser->relationLoaded('activeCategories')) {
            return [];
        }

        return $eloquentUser->activeCategories->pluck('id')->all();
    }

    /**
     * @return array<int, string>
     */
    private static function extractRoleIds(EloquentUser $eloquentUser): array
    {
        if (!$eloquentUser->relationLoaded('roles')) {
            return [];
        }

        return $eloquentUser->roles->pluck('id')->all();
    }
}
