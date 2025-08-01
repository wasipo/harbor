<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Category;

interface UserCategoryRepositoryInterface
{
    /**
     * IDでカテゴリを取得
     */
    public function findById(UserCategoryId $id): ?UserCategory;

    /**
     * カテゴリを保存
     */
    public function save(UserCategory $category): UserCategory;

}