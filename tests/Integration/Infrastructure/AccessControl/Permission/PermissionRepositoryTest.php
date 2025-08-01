<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\AccessControl\Permission;

use App\Domain\AccessControl\Permission\Permission;
use App\Domain\AccessControl\Permission\PermissionId;
use App\Domain\AccessControl\Permission\PermissionKey;
use App\Domain\AccessControl\Permission\PermissionName;
use App\Infrastructure\AccessControl\Permission\PermissionRepository;
use App\Models\Permission as EloquentPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermissionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PermissionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PermissionRepository();
    }

    #[Test]
    public function 正常系_IDで検索成功(): void
    {
        // Arrange
        $eloquentPermission = EloquentPermission::factory()->create([
            'id' => '01K12GYPNEQP8K2Q17CJSNVEGZ',
            'key' => 'users.create',
            'resource' => 'users',
            'action' => 'create',
            'display_name' => 'Create Users',
        ]);
        $permissionId = PermissionId::fromString('01K12GYPNEQP8K2Q17CJSNVEGZ');

        // Act
        $permission = $this->repository->findById($permissionId);

        // Assert
        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('01K12GYPNEQP8K2Q17CJSNVEGZ', $permission->id->toString());
        $this->assertEquals('users.create', $permission->key->value);
        $this->assertEquals('users', $permission->resource);
        $this->assertEquals('create', $permission->action);
    }

    #[Test]
    public function 正常系_存在しないIDはnull(): void
    {
        // Arrange
        $nonExistentId = PermissionId::create();

        // Act
        $permission = $this->repository->findById($nonExistentId);

        // Assert
        $this->assertNull($permission);
    }

    #[Test]
    public function 正常系_キーで検索成功(): void
    {
        // Arrange
        EloquentPermission::factory()->create([
            'key' => 'posts.edit',
            'resource' => 'posts',
            'action' => 'edit',
            'display_name' => 'Edit Posts',
        ]);

        // Act
        $permission = $this->repository->findByKey('posts.edit');

        // Assert
        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('posts.edit', $permission->key->value);
        $this->assertEquals('posts', $permission->resource);
        $this->assertEquals('edit', $permission->action);
    }

    #[Test]
    public function 正常系_複数キーで検索(): void
    {
        // Arrange
        EloquentPermission::factory()->create(['key' => 'users.create']);
        EloquentPermission::factory()->create(['key' => 'users.edit']);
        EloquentPermission::factory()->create(['key' => 'posts.create']);

        // Act
        $permissions = $this->repository->findByKeys(['users.create', 'users.edit']);

        // Assert
        $this->assertCount(2, $permissions);
        $keys = array_map(fn($p) => $p->key->value, $permissions);
        $this->assertContains('users.create', $keys);
        $this->assertContains('users.edit', $keys);
    }

    #[Test]
    public function 正常系_リソースで検索(): void
    {
        // Arrange
        EloquentPermission::factory()->create(['key' => 'categories.create', 'resource' => 'categories', 'action' => 'create']);
        EloquentPermission::factory()->create(['key' => 'categories.edit', 'resource' => 'categories', 'action' => 'edit']);
        EloquentPermission::factory()->create(['key' => 'users.create', 'resource' => 'users', 'action' => 'create']);

        // Act
        $permissions = $this->repository->findByResource('categories');

        // Assert
        $this->assertCount(2, $permissions);
        foreach ($permissions as $permission) {
            $this->assertEquals('categories', $permission->resource);
        }
    }

    #[Test]
    public function 正常系_新規権限作成(): void
    {
        // Arrange
        $permission = Permission::create(
            key: 'roles.delete',
            name: 'Delete Roles',
            description: 'Permission to delete roles'
        );

        // Act
        $savedPermission = $this->repository->create($permission);

        // Assert
        $this->assertInstanceOf(Permission::class, $savedPermission);
        $this->assertEquals('roles.delete', $savedPermission->key->value);
        $this->assertEquals('roles', $savedPermission->resource);
        $this->assertEquals('delete', $savedPermission->action);
        $this->assertEquals('Delete Roles', $savedPermission->name->value);

        $this->assertDatabaseHas('permissions', [
            'key' => 'roles.delete',
            'resource' => 'roles',
            'action' => 'delete',
            'display_name' => 'Delete Roles',
            'description' => 'Permission to delete roles',
        ]);
    }

    #[Test]
    public function 正常系_全件取得(): void
    {
        // Arrange
        EloquentPermission::factory()->count(3)->create();

        // Act
        $permissions = $this->repository->all();

        // Assert
        $this->assertCount(3, $permissions);
        foreach ($permissions as $permission) {
            $this->assertInstanceOf(Permission::class, $permission);
        }
    }

    #[Test]
    public function 正常系_キー存在チェック(): void
    {
        // Arrange
        EloquentPermission::factory()->create(['key' => 'existing.permission']);

        // Act & Assert
        $this->assertTrue($this->repository->existsByKey('existing.permission'));
        $this->assertFalse($this->repository->existsByKey('non.existing.permission'));
    }
}