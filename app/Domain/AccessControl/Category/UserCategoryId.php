<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Category;

use App\Domain\Shared\ValueObjects\AbstractUlidId;

/**
 * User Category ID value object
 */
final readonly class UserCategoryId extends AbstractUlidId
{
    // 空でOK - 全ての実装は AbstractUlidId に委譲
}