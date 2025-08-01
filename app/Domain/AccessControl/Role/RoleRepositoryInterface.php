<?php

namespace App\Domain\AccessControl\Role;

use Illuminate\Support\Collection;

// todo: policyを使う場合に、Levelが必要かどうか
interface RoleRepositoryInterface
{
    public function findById(RoleId $id): ?Role;

    public function findByName(string $name): ?Role;

    /**
     * @return Collection<int, Role>
     */
    public function findAll(): Collection;

    public function save(Role $role): Role;

    public function delete(Role $role): bool;

    public function existsByName(string $name): bool;

    public function existsById(RoleId $id): bool;

}