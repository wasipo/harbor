<?php

namespace App\Infrastructure\AccessControl\Role;

use App\Domain\AccessControl\Role\Role;
use App\Domain\AccessControl\Role\RoleFactory;
use App\Domain\AccessControl\Role\RoleId;
use App\Domain\AccessControl\Role\RoleRepositoryInterface;
use App\Models\Role as EloquentRole;
use Illuminate\Support\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function findById(RoleId $id): ?Role
    {
        $eloquentRole = EloquentRole::where('id', $id->toString())->first();

        return $eloquentRole ? RoleFactory::fromEloquent($eloquentRole) : null;
    }

    public function findByName(string $name): ?Role
    {
        $eloquentRole = EloquentRole::where('name', $name)->first();

        return $eloquentRole ? RoleFactory::fromEloquent($eloquentRole) : null;
    }

    /**
     * @return Collection<int, Role>
     */
    public function findAll(): Collection
    {
        $eloquentRoles = EloquentRole::all();

        return $eloquentRoles->map(fn (EloquentRole $eloquentRole) => RoleFactory::fromEloquent($eloquentRole));
    }

    public function save(Role $role): Role
    {
        $eloquentRole = EloquentRole::updateOrCreate(
            ['id' => $role->id->toString()],
            [
                'name' => $role->name,
                'display_name' => $role->displayName,
            ]
        );

        return RoleFactory::fromEloquent($eloquentRole);
    }

    public function delete(Role $role): bool
    {
        return EloquentRole::where('id', $role->id->toString())->delete() > 0;
    }

    public function existsByName(string $name): bool
    {
        return EloquentRole::where('name', $name)->exists();
    }

    public function existsById(RoleId $id): bool
    {
        return EloquentRole::where('id', $id->toString())->exists();
    }
}
