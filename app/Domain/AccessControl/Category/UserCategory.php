<?php

namespace App\Domain\AccessControl\Category;

use App\Domain\AccessControl\Permission\PermissionIdCollection;

readonly class UserCategory
{
    private function __construct(
        public UserCategoryId $id,
        public string $code,
        public string $name,
        public ?string $description,
        public bool $isActive,
        public PermissionIdCollection $permissionIds
    ) {}

    /**
     * 新規カテゴリ作成
     */
    public static function create(
        string $code,
        string $name,
        ?string $description = null,
        bool $isActive = true,
        ?PermissionIdCollection $permissionIds = null
    ): self {
        return new self(
            id: UserCategoryId::create(),
            code: $code,
            name: $name,
            description: $description,
            isActive: $isActive,
            permissionIds: $permissionIds ?? PermissionIdCollection::empty()
        );
    }

    /**
     * 永続化からの復元
     */
    public static function reconstitute(
        UserCategoryId $id,
        string $code,
        string $name,
        ?string $description,
        bool $isActive,
        PermissionIdCollection $permissionIds
    ): self {
        return new self(
            id: $id,
            code: $code,
            name: $name,
            description: $description,
            isActive: $isActive,
            permissionIds: $permissionIds
        );
    }

    public function isAdmin(): bool
    {
        return $this->code === 'admin';
    }

    public function isEngineer(): bool
    {
        return $this->code === 'engineer';
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }
}
