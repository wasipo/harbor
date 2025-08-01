<?php

namespace App\Domain\Identity;

use InvalidArgumentException;

readonly class Name
{
    public function __construct(public string $value)
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException('Name cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Name cannot exceed 255 characters');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
