<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Permission;

use App\Domain\Shared\Collections\CollectionBehavior;
use App\Domain\Shared\Collections\UlidIdCollection;
use Closure;

/**
 * @extends UlidIdCollection<PermissionId>
 */
final class PermissionIdCollection extends UlidIdCollection
{
    /**
     * 権限IDは重複を許可しない
     */
    protected function behavior(): CollectionBehavior
    {
        return CollectionBehavior::STRICT_NO_DUPLICATES;
    }
    /**
     * @return Closure(string): PermissionId
     */
    protected static function idFactory(): Closure
    {
        return fn (string $s) => PermissionId::fromString($s);
    }

    /**
     * @param array<int, string> $permissionIds
     */
    public function including(array $permissionIds): self
    {
        $additional = self::fromStrings($permissionIds);

        return new self([...$this->all(), ...$additional->all()]);
    }
}