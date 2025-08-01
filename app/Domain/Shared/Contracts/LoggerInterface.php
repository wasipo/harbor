<?php

declare(strict_types=1);

namespace App\Domain\Shared\Contracts;

interface LoggerInterface
{
    /**
     * 情報レベルのログを記録
     *
     * @param  array<string, mixed>  $context
     */
    public function info(string $message, array $context = []): void;

    /**
     * 警告レベルのログを記録
     *
     * @param  array<string, mixed>  $context
     */
    public function warning(string $message, array $context = []): void;

    /**
     * エラーレベルのログを記録
     *
     * @param  array<string, mixed>  $context
     */
    public function error(string $message, array $context = []): void;

    /**
     * デバッグレベルのログを記録
     *
     * @param  array<string, mixed>  $context
     */
    public function debug(string $message, array $context = []): void;
}
