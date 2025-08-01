<?php

declare(strict_types=1);

namespace App\Infrastructure\AccessControl\Category;

use App\Domain\AccessControl\Category\UserCategory;
use App\Domain\AccessControl\Category\UserCategoryFactory;
use App\Domain\AccessControl\Category\UserCategoryId;
use App\Domain\AccessControl\Category\UserCategoryRepositoryInterface;
use App\Models\UserCategory as EloquentUserCategory;
use Illuminate\Support\Str;

class UserCategoryRepository implements UserCategoryRepositoryInterface
{
    /**
     * IDでカテゴリを取得
     */
    public function findById(UserCategoryId $id): ?UserCategory
    {
        $eloquentCategory = EloquentUserCategory::with('permissions')->find($id->toString());
        
        if ($eloquentCategory === null) {
            return null;
        }
        
        return UserCategoryFactory::fromEloquent($eloquentCategory);
    }

    /**
     * カテゴリを保存
     */
    public function save(UserCategory $category): UserCategory
    {
        $eloquentCategory = EloquentUserCategory::updateOrCreate(
            ['id' => $category->id->toString()],
            [
                'code' => $category->code,
                'name' => $category->name,
                'description' => $category->description,
                'is_active' => $category->isActive,
            ]
        );
        
        return UserCategoryFactory::fromEloquent($eloquentCategory);
    }

}