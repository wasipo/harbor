<?php

declare(strict_types=1);

namespace App\Domain\Shared\Contracts;

interface LoggerInterface
{
    /**
     * 情報レベルのログを記録
     */
    public function info(string $message, array $context = []): void;

    /**
     * 警告レベルのログを記録
     */
    public function warning(string $message, array $context = []): void;

    /**
     * エラーレベルのログを記録
     */
    public function error(string $message, array $context = []): void;

    /**
     * デバッグレベルのログを記録
     */
    public function debug(string $message, array $context = []): void;
}