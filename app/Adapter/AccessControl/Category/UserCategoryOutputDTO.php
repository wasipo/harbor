<?php

declare(strict_types=1);

namespace App\Adapter\AccessControl\Category;

use App\Domain\AccessControl\Category\UserCategory;

readonly class UserCategoryOutputDTO
{
    public function __construct(
        public int $id,
        public string $code,
        public string $name,
        public ?string $description,
        public bool $is_active,
    ) {}

    public static function fromModel(UserCategory $category): self
    {
        return new self(
            id: $category->id,
            code: $category->code,
            name: $category->name,
            description: $category->description,
            is_active: $category->isActive,
        );
    }

    /**
     * @param  array<int, UserCategory>  $categories
     * @return array<int, UserCategoryOutputDTO>
     */
    public static function fromArray(array $categories): array
    {
        return array_map(fn (UserCategory $category) => self::fromModel($category), $categories);
    }

    /**
     * @return array{
     *     id: int,
     *     code: string,
     *     name: string,
     *     description: string|null,
     *     is_active: bool
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];
    }
}
