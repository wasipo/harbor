<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Stringable;

/**
 * Base abstract class for all ID value objects
 * 
 * Supports both ULID (string) and Int based identifiers
 */
abstract readonly class AbstractId implements Stringable
{
    /**
     * @param string|int $value The identifier value
     */
    public function __construct(
        protected string|int $value
    ) {}

    /**
     * Get the raw value of the identifier
     * 
     * @return string|int
     */
    public function value(): string|int
    {
        return $this->value;
    }

    /**
     * Get string representation of the identifier
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Check equality with another ID
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Magic method for string conversion
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}