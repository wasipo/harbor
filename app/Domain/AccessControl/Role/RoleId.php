<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Role;

use App\Domain\Shared\ValueObjects\AbstractUlidId;

/**
 * Role ID value object
 */
final readonly class RoleId extends AbstractUlidId
{
    // 空でOK - 全ての実装は AbstractUlidId に委譲
}
