<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Category;

use App\Domain\Shared\Collections\CollectionBehavior;
use App\Domain\Shared\Collections\UlidIdCollection;
use Closure;
use DomainException;

/**
 * @extends UlidIdCollection<UserCategoryId>
 */
final class CategoryIdCollection extends UlidIdCollection
{
    /**
     * カテゴリIDは重複を許可しない
     */
    protected function behavior(): CollectionBehavior
    {
        return CollectionBehavior::STRICT_NO_DUPLICATES;
    }
    /**
     * @return Closure(string): UserCategoryId
     */
    protected static function idFactory(): Closure
    {
        return fn (string $s) => UserCategoryId::fromString($s);
    }

    /**
     * @param array<int, string> $categoryIds
     */
    public function including(array $categoryIds): self
    {
        $additional = self::fromStrings($categoryIds);

        return new self([...$this->all(), ...$additional->all()]);
    }

    /**
     * 主所属カテゴリIDを取得
     * ビジネスルール：最初のカテゴリが主所属
     * 
     * @return UserCategoryId
     */
    public function getPrimaryId(): UserCategoryId
    {
        $id = $this->first();
        
        if ($id === null) {
            throw new DomainException('No categories in collection to determine primary ID');
        }
        
        return $id;
    }
}
