<?php

declare(strict_types=1);

namespace App\Presenter\Identity;

use App\Adapter\Identity\AuthOutputDTO;

interface AuthResponseBuilderInterface
{
    /**
     * 認証成功レスポンスを構築
     *
     * @return array<string, mixed>
     */
    public function build(AuthOutputDTO $dto): array;
}