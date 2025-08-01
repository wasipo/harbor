<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Permission;

use InvalidArgumentException;
use Stringable;

/**
 * Permission Name Value Object
 * 
 * 権限名を表す値オブジェクト
 * 例: '記事作成', 'ユーザー編集', '管理者アクセス'
 */
final readonly class PermissionName implements Stringable
{
    private const MAX_LENGTH = 100;
    
    public function __construct(
        public string $value
    ) {
        if ($value === '') {
            throw new InvalidArgumentException('Permission name cannot be empty');
        }
        
        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Permission name cannot exceed %d characters', self::MAX_LENGTH)
            );
        }
    }
    
    public function value(): string
    {
        return $this->value;
    }
    
    public function toString(): string
    {
        return $this->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
    
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}