<?php

namespace Tests\Integration\Infrastructure\AccessControl\Role;

use App\Domain\AccessControl\Role\Role;
use App\Domain\AccessControl\Role\RoleId;
use App\Infrastructure\AccessControl\Role\RoleRepository;
use App\Models\Role as EloquentRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\UnitTestCase;

class RoleRepositoryTest extends UnitTestCase
{
    use RefreshDatabase;

    private RoleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new RoleRepository;
    }

    public function test_正常系_i_dで検索成功(): void
    {
        // Arrange
        $eloquentRole = EloquentRole::factory()->create([
            'id' => '01K0EARXJ6NA68S3XGQKEN04FV',
            'name' => 'admin',
            'display_name' => 'Administrator',
        ]);

        $roleId = RoleId::fromString('01K0EARXJ6NA68S3XGQKEN04FV');

        // Act
        $role = $this->repository->findById($roleId);

        // Assert
        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('01K0EARXJ6NA68S3XGQKEN04FV', $role->id->toString());
        $this->assertEquals('admin', $role->name);
        $this->assertEquals('Administrator', $role->displayName);
    }

    public function test_正常系_存在しない_i_dはnull(): void
    {
        // Arrange
        $nonExistentId = RoleId::fromString('01K0EAT5EBYR2SV2EHSCQ7WDK1');

        // Act
        $role = $this->repository->findById($nonExistentId);

        // Assert
        $this->assertNull($role);
    }

    public function test_正常系_名前で検索成功(): void
    {
        // Arrange
        $eloquentRole = EloquentRole::factory()->create([
            'id' => '01K0EARXJ6NA68S3XGQKEN04FV',
            'name' => 'admin',
            'display_name' => 'Administrator',
        ]);

        // Act
        $role = $this->repository->findByName('admin');

        // Assert
        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('admin', $role->name);
    }

    public function test_正常系_全件取得成功(): void
    {
        // Arrange
        EloquentRole::factory()->create([
            'id' => '01K0EARXJ6NA68S3XGQKEN04FV',
            'name' => 'admin',
            'display_name' => 'Administrator',
        ]);

        EloquentRole::factory()->create([
            'id' => '01K0EAT5EBYR2SV2EHSCQ7WDK2',
            'name' => 'editor',
            'display_name' => 'Editor',
        ]);

        // Act
        $roles = $this->repository->findAll();

        // Assert
        $this->assertCount(2, $roles);
        $this->assertContainsOnlyInstancesOf(Role::class, $roles);
    }

    public function test_正常系_ロール新規作成(): void
    {
        // Arrange
        $role = Role::create(
            name: 'admin',
            displayName: 'Administrator'
        );

        // Act
        $savedRole = $this->repository->save($role);

        // Assert
        $this->assertDatabaseHas('roles', [
            'id' => $savedRole->id->toString(),
            'name' => 'admin',
            'display_name' => 'Administrator',
        ]);
        $this->assertEquals('admin', $savedRole->name);
        $this->assertEquals('Administrator', $savedRole->displayName);
    }

    public function test_正常系_ロール更新(): void
    {
        // Arrange
        $eloquentRole = EloquentRole::factory()->create([
            'id' => '01K0EARXJ6NA68S3XGQKEN04FV',
            'name' => 'admin',
            'display_name' => 'Administrator',
        ]);

        $updatedRole = Role::reconstitute(
            id: RoleId::fromString('01K0EARXJ6NA68S3XGQKEN04FV'),
            name: 'admin',
            displayName: 'Super Administrator',
            permissionIds: \App\Domain\AccessControl\Permission\PermissionIdCollection::empty()
        );

        // Act
        $savedRole = $this->repository->save($updatedRole);

        // Assert
        $this->assertDatabaseHas('roles', [
            'id' => '01K0EARXJ6NA68S3XGQKEN04FV',
            'display_name' => 'Super Administrator',
        ]);
        $this->assertEquals('Super Administrator', $savedRole->displayName);
    }

    public function test_正常系_削除成功(): void
    {
        // Arrange
        $eloquentRole = EloquentRole::factory()->create([
            'id' => '01K0EARXJ6NA68S3XGQKEN04FV',
            'name' => 'admin',
            'display_name' => 'Administrator',
        ]);

        $role = Role::reconstitute(
            id: RoleId::fromString('01K0EARXJ6NA68S3XGQKEN04FV'),
            name: 'admin',
            displayName: 'Administrator',
            permissionIds: \App\Domain\AccessControl\Permission\PermissionIdCollection::empty()
        );

        // Act
        $result = $this->repository->delete($role);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('roles', [
            'id' => '01K0EARXJ6NA68S3XGQKEN04FV',
        ]);
    }

    public function test_正常系_名前で存在確認(): void
    {
        // Arrange
        EloquentRole::factory()->create([
            'id' => '01K0EARXJ6NA68S3XGQKEN04FV',
            'name' => 'admin',
            'display_name' => 'Administrator',
        ]);

        // Act & Assert
        $this->assertTrue($this->repository->existsByName('admin'));
        $this->assertFalse($this->repository->existsByName('nonexistent'));
    }

    public function test_正常系_i_dで存在確認(): void
    {
        // Arrange
        EloquentRole::factory()->create([
            'id' => '01K0EARXJ6NA68S3XGQKEN04FV',
            'name' => 'admin',
            'display_name' => 'Administrator',
        ]);

        // Act & Assert
        $this->assertTrue($this->repository->existsById(RoleId::fromString('01K0EARXJ6NA68S3XGQKEN04FV')));
        $this->assertFalse($this->repository->existsById(RoleId::fromString('01K0EAT5EBYR2SV2EHSCQ7WDK1')));
    }
}
