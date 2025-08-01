<?php

namespace App\Domain\Identity;

use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\AccessControl\Category\UserCategoryId;
use App\Domain\AccessControl\Role\RoleIdCollection;
use App\Models\User as EloquentUser;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findById(UserId $id): ?User;

    public function findByEmail(Email $email): ?User;

    public function findActiveUsers(int $perPage = 15, ?string $search = null, ?string $category = null): LengthAwarePaginator;

    public function add(User $user, string $plainPassword): User;

    public function update(User $user, ?string $plainPassword = null): User;

    public function delete(User $user): bool;

    public function existsByEmail(Email $email): bool;


    /**
     * Find Eloquent user by email (for authentication purposes)
     */
    public function findEloquentByEmail(Email $email): ?EloquentUser;

    /**
     * ユーザーにカテゴリを割り当てる
     */
    public function assignCategories(
        UserId $userId,
        CategoryIdCollection $categoryIds,
        UserCategoryId $primaryCategoryId
    ): void;

    /**
     * ユーザーにロールを割り当てる
     */
    public function assignRoles(
        UserId $userId,
        RoleIdCollection $roleIds,
        ?UserId $assignedBy = null
    ): void;
}
