<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Permission;

/**
 * Permission Entity
 * 
 * 権限を表すエンティティ
 */
readonly class Permission
{
    private function __construct(
        public PermissionId $id,
        public PermissionKey $key,
        public string $resource,
        public string $action,
        public PermissionName $name,
        public ?string $description = null,
    ) {}

    /**
     * 新規作成
     */
    public static function create(
        string $key,
        string $name,
        ?string $description = null
    ): self {
        // keyからresourceとactionを抽出
        [$resource, $action] = explode('.', $key, 2);
        
        return new self(
            id: PermissionId::create(),
            key: new PermissionKey($key),
            resource: $resource,
            action: $action,
            name: new PermissionName($name),
            description: $description
        );
    }

    /**
     * 永続化からの復元
     */
    public static function reconstitute(
        PermissionId $id,
        PermissionKey $key,
        string $resource,
        string $action,
        PermissionName $name,
        ?string $description = null
    ): self {
        return new self($id, $key, $resource, $action, $name, $description);
    }

    /**
     * 同一性の判定
     */
    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }
}