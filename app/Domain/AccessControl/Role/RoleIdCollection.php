<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Role;

use App\Domain\Shared\Collections\CollectionBehavior;
use App\Domain\Shared\Collections\UlidIdCollection;
use Closure;

/**
 * @extends UlidIdCollection<RoleId>
 */
final class RoleIdCollection extends UlidIdCollection
{
    /**
     * ロールIDは重複を許可しない
     */
    protected function behavior(): CollectionBehavior
    {
        return CollectionBehavior::STRICT_NO_DUPLICATES;
    }
    /**
     * @return Closure(string): RoleId
     */
    protected static function idFactory(): Closure
    {
        return fn (string $s) => RoleId::fromString($s);
    }

    /**
     * @param array<int, string> $roleIds
     */
    public function including(array $roleIds): self
    {
        $additional = self::fromStrings($roleIds);

        return new self([...$this->all(), ...$additional->all()]);
    }
}
