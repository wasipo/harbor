<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Permission;

use App\Domain\Shared\ValueObjects\AbstractUlidId;

/**
 * Permission ID value object
 */
final readonly class PermissionId extends AbstractUlidId
{
    // 空でOK - 全ての実装は AbstractUlidId に委譲
}