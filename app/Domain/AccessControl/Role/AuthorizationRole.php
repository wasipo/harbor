<?php

namespace App\Domain\AccessControl\Role;

readonly class AuthorizationRole
{
    /**
     * @param  array<string, array<int, string>>  $permissions  権限構造: ['users' => ['create', 'read', 'update'], 'posts' => ['read', 'delete']]
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $displayName,
        public ?string $description,
        public array $permissions,
    ) {}

    /**
     * 権限をチェックする
     *
     * @param  string  $permission  権限文字列 (例: "users.create", "posts.read")
     */
    public function hasPermission(string $permission): bool
    {
        // 権限配列が空の場合は権限なし
        if (empty($this->permissions)) {
            return false;
        }

        // "resource.action" 形式でない場合は無効
        $parts = explode('.', $permission, 2);
        if (count($parts) !== 2) {
            return false;
        }

        [$resource, $action] = $parts;

        // リソースに対する権限が存在し、かつ指定されたアクションが許可されているかチェック
        return isset($this->permissions[$resource]) &&
            in_array($action, $this->permissions[$resource], true);
    }

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }
}
