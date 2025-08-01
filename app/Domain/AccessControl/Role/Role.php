<?php

namespace App\Domain\AccessControl\Role;

use App\Domain\AccessControl\Permission\PermissionIdCollection;

readonly class Role
{
    private function __construct(
        public RoleId $id,
        public string $name,
        public string $displayName,
        public PermissionIdCollection $permissionIds
    ) {}

    /**
     * 新規ロール作成
     */
    public static function create(
        string $name,
        string $displayName,
        ?PermissionIdCollection $permissionIds = null
    ): self {
        return new self(
            id: RoleId::create(),
            name: $name,
            displayName: $displayName,
            permissionIds: $permissionIds ?? PermissionIdCollection::empty()
        );
    }

    /**
     * 永続化からの復元
     */
    public static function reconstitute(
        RoleId $id,
        string $name,
        string $displayName,
        PermissionIdCollection $permissionIds
    ): self {
        return new self(
            id: $id,
            name: $name,
            displayName: $displayName,
            permissionIds: $permissionIds
        );
    }

    // Domain behaviors
    public function changeDisplayName(string $displayName): self
    {
        return self::reconstitute(
            $this->id,
            $this->name,
            $displayName,
            $this->permissionIds
        );
    }

    // Note: Permission checks are handled through Repository/Service layer
    // as permissions are now stored in a separate table

    // Equality
    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }
}
