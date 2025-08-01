<?php

namespace App\Infrastructure\Identity;

use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\AccessControl\Category\UserCategoryId;
use App\Domain\AccessControl\Role\RoleIdCollection;
use App\Domain\Identity\Email;
use App\Domain\Identity\User;
use App\Domain\Identity\UserFactory;
use App\Domain\Identity\UserId;
use App\Domain\Identity\UserRepositoryInterface;
use App\Infrastructure\Shared\Security\PasswordHasher;
use App\Models\User as EloquentUser;
use App\Models\UserCategoryAssignment;
use App\Models\UserRole;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use RuntimeException;

final readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private PasswordHasher $passwordHasher
    ) {}

    public function findById(UserId $id): ?User
    {
        $eloquentUser = EloquentUser::with(['activeCategories', 'roles'])
            ->where('id', $id->toString())
            ->first();

        return $eloquentUser ? UserFactory::fromEloquent($eloquentUser) : null;
    }

    public function findByEmail(Email $email): ?User
    {
        $eloquentUser = EloquentUser::with(['activeCategories', 'roles'])
            ->where('email', $email->value)
            ->first();

        return $eloquentUser ? UserFactory::fromEloquent($eloquentUser) : null;
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function findActiveUsers(int $perPage = 15, ?string $search = null, ?string $category = null): LengthAwarePaginator
    {
        $query = EloquentUser::with(['activeCategories', 'roles']);

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category) {
            $query->whereHas('activeCategories', function ($q) use ($category) {
                $q->where('code', $category);
            });
        }

        // Transform the paginator to contain domain models instead of Eloquent models
        $paginator = $query->paginate($perPage);

        // Transform collection items from EloquentUser to User
        $transformed = $paginator->getCollection()->map(function (EloquentUser $eloquentUser): User {
            return UserFactory::fromEloquent($eloquentUser);
        });

        // Create a new paginator with the transformed collection
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $transformed,
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            ['path' => $paginator->path()]
        );
    }

    public function add(User $user, string $plainPassword): User
    {
        $eloquentUser = EloquentUser::create([
            'id' => $user->id->toString(),
            'name' => $user->name->value,
            'email' => $user->email->value,
            'password' => $this->passwordHasher->hash($plainPassword),
            'is_active' => $user->isActive(),
            'email_verified_at' => $user->emailVerifiedAt,
        ]);

        $refreshed = $eloquentUser->fresh(['activeCategories', 'roles']);
        if (!$refreshed instanceof EloquentUser) {
            throw new RuntimeException('Failed to refresh user');
        }

        return UserFactory::fromEloquent($refreshed);
    }

    public function update(User $user, ?string $plainPassword = null): User
    {
        $eloquentUser = EloquentUser::where('id', $user->id->toString())
            ->lockForUpdate()
            ->firstOrFail();

        $eloquentUser->fill([
            'name' => $user->name->value,
            'email' => $user->email->value,
            'is_active' => $user->isActive(),
            'email_verified_at' => $user->emailVerifiedAt,
        ]);

        if ($plainPassword !== null) {
            $eloquentUser->password = $this->passwordHasher->hash($plainPassword);
        }

        $eloquentUser->save();

        $refreshed = $eloquentUser->fresh(['activeCategories', 'roles']);
        if (!$refreshed instanceof EloquentUser) {
            throw new RuntimeException('Failed to refresh user');
        }

        return UserFactory::fromEloquent($refreshed);
    }

    public function delete(User $user): bool
    {
        $eloquentUser = EloquentUser::where('id', $user->id->toString())->first();

        if (!$eloquentUser) {
            return false; // User not found
        }

        return (bool) $eloquentUser->delete();
    }

    public function existsByEmail(Email $email): bool
    {
        return EloquentUser::where('email', $email->value)->exists();
    }

    public function findEloquentByEmail(Email $email): ?EloquentUser
    {
        return EloquentUser::with(['activeCategories', 'roles'])
            ->where('email', $email->value)
            ->first();
    }

    /**
     * ユーザーにカテゴリを割り当てる
     */
    public function assignCategories(
        UserId $userId,
        CategoryIdCollection $categoryIds,
        UserCategoryId $primaryCategoryId
    ): void {
        if ($categoryIds->isEmpty()) {
            return;
        }

        $effectiveFrom = CarbonImmutable::now()->format('Y-m-d');
        $assignments = [];

        foreach ($categoryIds->all() as $categoryId) {
            $assignments[] = [
                'user_id' => $userId->toString(),
                'category_id' => $categoryId->toString(),
                'is_primary' => $categoryId->equals($primaryCategoryId),
                'effective_from' => $effectiveFrom,
                'effective_until' => null,
            ];
        }

        // fillAndInsertを使用してバルクインサート（Laravel 12.6+）
        UserCategoryAssignment::fillAndInsert($assignments);
    }

    /**
     * ユーザーにロールを割り当てる
     */
    public function assignRoles(
        UserId $userId,
        RoleIdCollection $roleIds,
        ?UserId $assignedBy = null
    ): void {
        if ($roleIds->isEmpty()) {
            return;
        }

        $assignedAt = CarbonImmutable::now();
        $assignments = [];

        foreach ($roleIds->all() as $roleId) {
            $assignments[] = [
                'user_id' => $userId->toString(),
                'role_id' => $roleId->toString(),
                'assigned_by' => $assignedBy?->toString(),
                'assigned_at' => $assignedAt,
            ];
        }

        // fillAndInsertを使用してバルクインサート（Laravel 12.6+）
        UserRole::fillAndInsert($assignments);
    }
}
