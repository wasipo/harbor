<?php

namespace Tests\Unit\Domain\AccessControl\Role;

use App\Domain\AccessControl\Permission\PermissionIdCollection;
use App\Domain\AccessControl\Role\Role;
use App\Domain\AccessControl\Role\RoleAssignmentService;
use App\Domain\AccessControl\Role\RoleId;
use App\Domain\Identity\AccountStatus;
use App\Domain\Identity\User;
use App\Models\UserRole;
use App\Models\User as EloquentUser;
use App\Models\Role as EloquentRole;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Factories\Domain\Identity\TestUserFactory;
use Tests\TestCase;

class RoleAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private RoleAssignmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoleAssignmentService();
    }

    #[Test]
    public function 正常系_ロール割り当て成功(): void
    {
        // Arrange
        $user = TestUserFactory::create();
        $eloquentUser = EloquentUser::factory()->create([
            'ulid' => $user->id->toString(),
            'name' => $user->name->value,
            'email' => $user->email->value,
            'is_active' => $user->isActive(),
        ]);
        
        $role = Role::reconstitute(
            id: RoleId::fromString(str()->ulid()),
            name: 'admin',
            displayName: '管理者',
            permissionIds: PermissionIdCollection::empty()
        );
        $eloquentRole = EloquentRole::create([
            'ulid' => $role->id->toString(),
            'name' => $role->name,
            'display_name' => $role->displayName,
        ]);
        
        // Act
        $this->service->assignRole($user, $role);
        
        // Assert
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $eloquentUser->id,
            'role_id' => $eloquentRole->id,
        ]);
    }

    #[Test]
    public function 正常系_割り当て者記録(): void
    {
        // Arrange
        $user = TestUserFactory::create();
        $eloquentUser = EloquentUser::factory()->create([
            'ulid' => $user->id->toString(),
            'name' => $user->name->value,
            'email' => $user->email->value,
            'is_active' => $user->isActive(),
        ]);
        
        $assignedBy = TestUserFactory::create();
        $eloquentAssignedBy = \App\Models\User::factory()->create([
            'ulid' => $assignedBy->id->toString(),
            'name' => $assignedBy->name->value,
            'email' => $assignedBy->email->value,
            'is_active' => $assignedBy->isActive(),
        ]);
        
        $role = Role::reconstitute(
            id: RoleId::fromString(str()->ulid()),
            name: 'admin',
            displayName: '管理者',
            permissionIds: PermissionIdCollection::empty()
        );
        $eloquentRole = EloquentRole::create([
            'ulid' => $role->id->toString(),
            'name' => $role->name,
            'display_name' => $role->displayName,
        ]);
        
        // Act
        $this->service->assignRole($user, $role, $assignedBy);
        
        // Assert
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $eloquentUser->id,
            'role_id' => $eloquentRole->id,
            'assigned_by' => $eloquentAssignedBy->id,
        ]);
    }

    #[Test]
    public function 正常系_重複ロール割り当てスキップ(): void
    {
        // Arrange
        $user = TestUserFactory::create();
        $eloquentUser = EloquentUser::factory()->create([
            'ulid' => $user->id->toString(),
            'name' => $user->name->value,
            'email' => $user->email->value,
            'is_active' => $user->isActive(),
        ]);
        
        $role = Role::reconstitute(
            id: RoleId::fromString(str()->ulid()),
            name: 'admin',
            displayName: '管理者',
            permissionIds: PermissionIdCollection::empty()
        );
        $eloquentRole = EloquentRole::create([
            'ulid' => $role->id->toString(),
            'name' => $role->name,
            'display_name' => $role->displayName,
        ]);
        
        // ロールを事前に割り当て
        UserRole::create([
            'user_id' => $eloquentUser->id,
            'role_id' => $eloquentRole->id,
            'assigned_at' => now(),
        ]);
        
        // Act
        $this->service->assignRole($user, $role);
        
        // Assert - 重複レコードが作られていない
        $count = UserRole::where('user_id', $eloquentUser->id)
            ->where('role_id', $eloquentRole->id)
            ->count();
        $this->assertEquals(1, $count);
    }

    #[Test]
    public function 異常系_非アクティブユーザー拒否(): void
    {
        // Arrange
        $user = TestUserFactory::create(
            status: AccountStatus::SUSPENDED
        );
        $eloquentUser = EloquentUser::factory()->create([
            'ulid' => $user->id->toString(),
            'name' => $user->name->value,
            'email' => $user->email->value,
            'is_active' => $user->isActive(),
        ]);
        
        $role = Role::reconstitute(
            id: RoleId::fromString(str()->ulid()),
            name: 'admin',
            displayName: '管理者',
            permissionIds: PermissionIdCollection::empty()
        );
        $eloquentRole = EloquentRole::create([
            'ulid' => $role->id->toString(),
            'name' => $role->name,
            'display_name' => $role->displayName,
        ]);
        
        // Act & Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot assign role to inactive user');
        
        $this->service->assignRole($user, $role);
    }

    #[Test]
    public function 正常系_ロール剥奪成功(): void
    {
        // Arrange
        $user = TestUserFactory::create();
        $eloquentUser = EloquentUser::factory()->create([
            'ulid' => $user->id->toString(),
            'name' => $user->name->value,
            'email' => $user->email->value,
            'is_active' => $user->isActive(),
        ]);
        
        $role = Role::reconstitute(
            id: RoleId::fromString(str()->ulid()),
            name: 'admin',
            displayName: '管理者',
            permissionIds: PermissionIdCollection::empty()
        );
        $eloquentRole = EloquentRole::create([
            'ulid' => $role->id->toString(),
            'name' => $role->name,
            'display_name' => $role->displayName,
        ]);
        
        // ロールを事前に割り当て
        UserRole::create([
            'user_id' => $eloquentUser->id,
            'role_id' => $eloquentRole->id,
            'assigned_at' => now(),
        ]);
        
        // Act
        $this->service->revokeRole($user, $role);
        
        // Assert
        $this->assertDatabaseMissing('user_roles', [
            'user_id' => $eloquentUser->id,
            'role_id' => $eloquentRole->id,
        ]);
    }

    #[Test]
    public function 正常系_存在しないロール剥奪(): void
    {
        // Arrange
        $user = TestUserFactory::create();
        $eloquentUser = EloquentUser::factory()->create([
            'ulid' => $user->id->toString(),
            'name' => $user->name->value,
            'email' => $user->email->value,
            'is_active' => $user->isActive(),
        ]);
        
        $role = Role::reconstitute(
            id: RoleId::fromString(str()->ulid()),
            name: 'admin',
            displayName: '管理者',
            permissionIds: PermissionIdCollection::empty()
        );
        // Eloquentロールは作成しない
        
        // Act - エラーにならずに実行される
        $this->service->revokeRole($user, $role);
        
        // Assert - DBに変更がない
        $this->assertDatabaseCount('user_roles', 0);
    }

    #[Test]
    public function 正常系_全ロール剥奪(): void
    {
        // Arrange
        $user = TestUserFactory::create();
        $eloquentUser = EloquentUser::factory()->create([
            'ulid' => $user->id->toString(),
            'name' => $user->name->value,
            'email' => $user->email->value,
            'is_active' => $user->isActive(),
        ]);
        
        // 複数のロールを作成して割り当て
        for ($i = 1; $i <= 3; $i++) {
            $role = EloquentRole::create([
                'ulid' => str()->ulid(),
                'name' => "role_{$i}",
                'display_name' => "ロール{$i}",
            ]);
            
            UserRole::create([
                'user_id' => $eloquentUser->id,
                'role_id' => $role->id,
                'assigned_at' => now(),
            ]);
        }
        
        // Act
        $this->service->revokeAllRoles($user);
        
        // Assert
        $this->assertDatabaseMissing('user_roles', [
            'user_id' => $eloquentUser->id,
        ]);
    }

    #[Test]
    public function 境界値_大量ロール割り当て(): void
    {
        // Arrange
        $user = TestUserFactory::create();
        $eloquentUser = EloquentUser::factory()->create([
            'ulid' => $user->id->toString(),
            'name' => $user->name->value,
            'email' => $user->email->value,
            'is_active' => $user->isActive(),
        ]);
        
        $roles = [];
        $eloquentRoles = [];
        for ($i = 1; $i <= 10; $i++) {
            $role = Role::reconstitute(
                id: RoleId::fromString(str()->ulid()),
                name: "role_{$i}",
                displayName: "ロール{$i}",
                permissionIds: PermissionIdCollection::empty()
            );
            
            $eloquentRole = EloquentRole::create([
                'ulid' => $role->id->toString(),
                'name' => $role->name,
                'display_name' => $role->displayName,
                ]);
            
            $roles[] = $role;
            $eloquentRoles[$role->id->toString()] = $eloquentRole;
        }
        
        // Act
        foreach ($roles as $role) {
            $this->service->assignRole($user, $role);
        }
        
        // Assert
        $this->assertDatabaseCount('user_roles', 10);
        foreach ($roles as $role) {
            $this->assertDatabaseHas('user_roles', [
                'user_id' => $eloquentUser->id,
                'role_id' => $eloquentRoles[$role->id->toString()]->id,
            ]);
        }
    }
}