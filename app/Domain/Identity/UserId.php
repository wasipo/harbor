<?php

declare(strict_types=1);

namespace App\Domain\Identity;

use App\Domain\Shared\ValueObjects\AbstractUlidId;

/**
 * User ID value object
 */
final readonly class UserId extends AbstractUlidId
{
    // 空でOK - 全ての実装は AbstractUlidId に委譲
}