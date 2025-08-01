<?php

declare(strict_types=1);

namespace App\Adapter\AccessControl\Category;

final readonly class CategoryDataDTO
{
    /**
     * @param  array<string>  $permissionKeys
     */
    public function __construct(
        public string $id,
        public string $code,
        public string $name,
        public bool $isPrimary,
        public array $permissionKeys = [],
    ) {}
}
