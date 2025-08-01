<?php

declare(strict_types=1);

namespace App\Adapter\Shared;

use App\Domain\Shared\Contracts\LoggerInterface;
use Illuminate\Support\Facades\Log;

final class LaravelLogger implements LoggerInterface
{
    /**
     * @inheritDoc
     */
    public function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug(string $message, array $context = []): void
    {
        Log::debug($message, $context);
    }
}
