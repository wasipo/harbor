<?php

namespace App\Domain\AccessControl\Role;

use App\Domain\Identity\UserId;
use Carbon\CarbonImmutable;

/**
 * ロール割り当てエンティティ
 * user_rolesテーブルをファーストクラスのエンティティとして扱う
 */
readonly class RoleAssignment
{
    public function __construct(
        public UserId $userId,
        public RoleId $roleId,
        public CarbonImmutable $assignedAt,
        public ?UserId $assignedBy = null
    ) {}

    /**
     * システムによる自動割り当てを作成
     */
    public static function assignBySystem(UserId $userId, RoleId $roleId): self
    {
        return new self(
            userId: $userId,
            roleId: $roleId,
            assignedAt: CarbonImmutable::now(),
            assignedBy: null
        );
    }

    /**
     * ユーザーによる割り当てを作成
     */
    public static function assignByUser(
        UserId $userId,
        RoleId $roleId,
        UserId $assignedBy
    ): self {
        return new self(
            userId: $userId,
            roleId: $roleId,
            assignedAt: CarbonImmutable::now(),
            assignedBy: $assignedBy
        );
    }

    /**
     * システムによる割り当てかどうか
     */
    public function isSystemAssigned(): bool
    {
        return $this->assignedBy === null;
    }

    /**
     * 割り当てから経過した日数
     */
    public function daysSinceAssignment(): int
    {
        return (int) $this->assignedAt->diffInDays(CarbonImmutable::now());
    }

    /**
     * 同一性の確認
     */
    public function equals(self $other): bool
    {
        return $this->userId->equals($other->userId) &&
               $this->roleId->equals($other->roleId);
    }
}
