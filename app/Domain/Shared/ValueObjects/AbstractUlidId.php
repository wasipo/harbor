<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Base class for ULID-based ID value objects
 *
 * Provides factory methods and ULID validation
 */
abstract readonly class AbstractUlidId extends AbstractId
{
    final public function __construct(string $value)
    {
        // Laravel's Str::isUlid() for validation
        if (!Str::isUlid($value)) {
            throw new InvalidArgumentException('Invalid ULID');
        }

        parent::__construct($value);
    }

    /**
     * Create a new ID with generated ULID
     */
    public static function create(): static
    {
        return new static((string) Str::ulid());
    }

    /**
     * Create ID from string value
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }
}
