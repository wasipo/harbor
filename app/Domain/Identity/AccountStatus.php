<?php

namespace App\Domain\Identity;

enum AccountStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canLogin(): bool
    {
        return $this === self::ACTIVE;
    }
}
