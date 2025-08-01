<?php

declare(strict_types=1);

namespace App\Domain\Shared\Collections;

/**
 * コレクションの重複制御仕様
 */
enum CollectionBehavior: string
{
    /**
     * 重複したらエラー
     */
    case STRICT_NO_DUPLICATES = 'strict';

    /**
     * 重複を静かに除去
     */
    case UNIQUE_SILENT = 'unique_silent';

    /**
     * 重複OK
     */
    case ALLOW_DUPLICATES = 'allow';
}
