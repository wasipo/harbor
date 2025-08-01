<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;

/**
 * Base class for integer-based ID value objects
 * 
 * For legacy systems or simple numeric identifiers
 */
abstract readonly class AbstractIntId extends AbstractId
{
    final public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('ID must be a positive integer');
        }
        
        parent::__construct($value);
    }

    /**
     * Create a new ID with random integer
     */
    public static function create(): static
    {
        return new static(random_int(1, PHP_INT_MAX));
    }

    /**
     * Create ID from integer value
     */
    public static function fromInt(int $value): static
    {
        return new static($value);
    }
}