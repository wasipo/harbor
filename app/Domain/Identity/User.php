<?php

declare(strict_types=1);

namespace App\Domain\Identity;

use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\AccessControl\Category\UserCategoryId;
use App\Domain\AccessControl\Role\RoleId;
use App\Domain\AccessControl\Role\RoleIdCollection;
use Carbon\CarbonImmutable;

/**
 * User aggregate root - Identity bounded context
 *
 * @psalm-immutable
 */
readonly class User
{
    public CategoryIdCollection $categoryIds;
    public RoleIdCollection $roleIds;
    public CarbonImmutable $emailVerifiedAt;

    /**
     * @param  UserId  $id  User identifier (ULID)
     * @param  Name  $name  User display name
     * @param  Email  $email  User email address
     * @param  AccountStatus  $status  Account status (ACTIVE/INACTIVE/SUSPENDED)
     * @param  CarbonImmutable|null  $emailVerifiedAt  Email verification timestamp (default: now)
     * @param  CategoryIdCollection|null  $categoryIds  User category assignments
     * @param  RoleIdCollection|null  $roleIds  User role assignments
     */
    private function __construct(
        public UserId $id,
        public Name $name,
        public Email $email,
        public AccountStatus $status = AccountStatus::ACTIVE,
        ?CarbonImmutable $emailVerifiedAt = null,
        ?CategoryIdCollection $categoryIds = null,
        ?RoleIdCollection $roleIds = null
    ) {
        $this->categoryIds = $categoryIds ?? CategoryIdCollection::empty();
        $this->roleIds = $roleIds ?? RoleIdCollection::empty();
        $this->emailVerifiedAt = $emailVerifiedAt ?? CarbonImmutable::now(); // todo: mail機能ができるまでは初期値を入力する
    }

    /**
     * 新規ユーザー作成
     */
    public static function create(
        Name $name,
        Email $email,
        AccountStatus $status = AccountStatus::ACTIVE,
        ?CarbonImmutable $emailVerifiedAt = null,
        ?CategoryIdCollection $categoryIds = null,
        ?RoleIdCollection $roleIds = null
    ): self {
        return new self(
            id: UserId::create(),
            name: $name,
            email: $email,
            status: $status,
            emailVerifiedAt: $emailVerifiedAt,
            categoryIds: $categoryIds,
            roleIds: $roleIds
        );
    }

    /**
     * 永続化からの復元
     */
    public static function reconstitute(
        UserId $id,
        Name $name,
        Email $email,
        AccountStatus $status = AccountStatus::ACTIVE,
        ?CarbonImmutable $emailVerifiedAt = null,
        ?CategoryIdCollection $categoryIds = null,
        ?RoleIdCollection $roleIds = null
    ): self {
        return new self(
            id: $id,
            name: $name,
            email: $email,
            status: $status,
            emailVerifiedAt: $emailVerifiedAt,
            categoryIds: $categoryIds,
            roleIds: $roleIds
        );
    }

    // Domain behaviors

    /**
     * Activate the user account
     *
     * @return self New User instance with ACTIVE status
     */
    public function activate(): self
    {
        return self::reconstitute(
            $this->id,
            $this->name,
            $this->email,
            AccountStatus::ACTIVE,
            $this->emailVerifiedAt,
            $this->categoryIds,
            $this->roleIds
        );
    }

    /**
     * Deactivate the user account
     *
     * @return self New User instance with INACTIVE status
     */
    public function deactivate(): self
    {
        return self::reconstitute(
            $this->id,
            $this->name,
            $this->email,
            AccountStatus::INACTIVE,
            $this->emailVerifiedAt,
            $this->categoryIds,
            $this->roleIds
        );
    }

    /**
     * Change user's display name
     *
     * @param  Name  $name  New display name
     * @return self New User instance with updated name
     */
    public function changeName(Name $name): self
    {
        return self::reconstitute(
            $this->id,
            $name,
            $this->email,
            $this->status,
            $this->emailVerifiedAt,
            $this->categoryIds,
            $this->roleIds
        );
    }

    /**
     * Change user's email address
     *
     * @param  Email  $email  New email address
     * @return self New User instance with updated email
     */
    public function changeEmail(Email $email): self
    {
        return self::reconstitute(
            $this->id,
            $this->name,
            $email,
            $this->status,
            $this->emailVerifiedAt,
            $this->categoryIds,
            $this->roleIds
        );
    }

    /**
     * Suspend the user account
     *
     * @return self New User instance with SUSPENDED status
     */
    public function suspend(): self
    {
        return self::reconstitute(
            $this->id,
            $this->name,
            $this->email,
            AccountStatus::SUSPENDED,
            $this->emailVerifiedAt,
            $this->categoryIds,
            $this->roleIds
        );
    }

    // Authorization methods (要修正: 外部サービスで実装)

    /**
     * Check if user account is active
     *
     * @return bool True if account status is ACTIVE
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * Check if user can login
     *
     * @return bool True if account allows login (ACTIVE or certain other statuses)
     */
    public function canLogin(): bool
    {
        return $this->status->canLogin();
    }

    /**
     * Assign a category to the user
     *
     * @param  UserCategoryId  $categoryId  Category to assign
     * @return self New User instance with added category
     */
    public function assignCategory(UserCategoryId $categoryId): self
    {
        $newCategoryIds = $this->categoryIds->including([$categoryId->toString()]);

        return self::reconstitute(
            $this->id,
            $this->name,
            $this->email,
            $this->status,
            $this->emailVerifiedAt,
            $newCategoryIds,
            $this->roleIds
        );
    }

    /**
     * Assign a role to the user
     *
     * @param  RoleId  $roleId  Role to assign
     * @return self New User instance with added role
     */
    public function assignRole(RoleId $roleId): self
    {
        $newRoleIds = $this->roleIds->including([$roleId->toString()]);

        return self::reconstitute(
            $this->id,
            $this->name,
            $this->email,
            $this->status,
            $this->emailVerifiedAt,
            $this->categoryIds,
            $newRoleIds
        );
    }

    // Equality

    /**
     * Check if this user is equal to another user
     * Equality is based on user ID only
     *
     * @param  self  $other  User to compare with
     * @return bool True if users have the same ID
     */
    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }
}
