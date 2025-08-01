<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Permission;

use InvalidArgumentException;
use Stringable;

/**
 * Permission Key Value Object
 * 
 * 権限キーを表す値オブジェクト
 * 例: 'posts.create', 'users.edit', 'admin.access'
 */
final readonly class PermissionKey implements Stringable
{
    private const PATTERN = '/^[a-z]+(\.[a-z]+)*$/';
    
    public function __construct(
        public string $value
    ) {
        if ($value === '') {
            throw new InvalidArgumentException('Permission key cannot be empty');
        }
        
        if (!preg_match(self::PATTERN, $value)) {
            throw new InvalidArgumentException(
                'Permission key must be lowercase letters separated by dots (e.g., posts.create)'
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