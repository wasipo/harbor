<?php

declare(strict_types=1);

namespace App\Adapter\RegisterUser;

readonly class AssignCategoriesCommand
{
    /**
     * @param  array<int, string>  $categoryIds  ULID文字列の配列
     */
    public function __construct(
        public array $categoryIds
    ) {}
}
