<?php

namespace Tests\Feature\AccessControl\Role;

use App\Application\AccessControl\Role\RevokeRoleAction;
use App\Adapter\AccessControl\Role\RevokeRoleCommand;
use App\Domain\AccessControl\Role\RoleAssignmentService;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use DomainException;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RevokeRoleActionTest extends TestCase
{
    use RefreshDatabase;

    private RevokeRoleAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = app(RevokeRoleAction::class);
    }

    #[Test]
    public function 正常系_ロール剥奪成功(): void
    {
        // Arrange
        $user = User::factory()->create();
        $role = Role::factory()->create();
        
        // ロールを事前に割り当て
        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'assigned_at' => now(),
        ]);
        
        $command = new RevokeRoleCommand(
            userId: $user->id,
            roleId: $role->ulid,
        );
        
        // Act
        ($this->action)($command);
        
        // Assert
        $this->assertDatabaseMissing('user_roles', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    #[Test]
    public function 正常系_特定ロールのみ剥奪(): void
    {
        // Arrange
        $user = User::factory()->create();
        $roleToRevoke = Role::factory()->create();
        $roleToKeep = Role::factory()->create();
        
        // 両方のロールを割り当て
        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $roleToRevoke->id,
            'assigned_at' => now(),
        ]);
        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $roleToKeep->id,
            'assigned_at' => now(),
        ]);
        
        $command = new RevokeRoleCommand(
            userId: $user->id,
            roleId: $roleToRevoke->ulid,
        );
        
        // Act
        ($this->action)($command);
        
        // Assert
        $this->assertDatabaseMissing('user_roles', [
            'user_id' => $user->id,
            'role_id' => $roleToRevoke->id,
        ]);
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $user->id,
            'role_id' => $roleToKeep->id,
        ]);
    }

    #[Test]
    public function 正常系_存在しないロール剥奪(): void
    {
        // Arrange
        $user = User::factory()->create();
        $role = Role::factory()->create();
        
        // ロールを割り当てていない
        
        $command = new RevokeRoleCommand(
            userId: $user->id,
            roleId: $role->ulid,
        );
        
        // Act - エラーにならずに実行される
        ($this->action)($command);
        
        // Assert - 変化がないことを確認
        $this->assertDatabaseMissing('user_roles', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    #[Test]
    public function 異常系_ユーザー不在エラー(): void
    {
        // Arrange
        $role = Role::factory()->create();
        
        $command = new RevokeRoleCommand(
            userId: 9999,
            roleId: $role->ulid,
        );
        
        // Act & Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User not found');
        
        ($this->action)($command);
    }

    #[Test]
    public function 異常系_ロール不在エラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        
        $command = new RevokeRoleCommand(
            userId: $user->id,
            roleId: str()->ulid(),
        );
        
        // Act & Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Role not found');
        
        ($this->action)($command);
    }

    #[Test]
    public function 正常系_トランザクションロールバック(): void
    {
        // Arrange
        $user = User::factory()->create();
        $role = Role::factory()->create();
        
        // ロールを事前に割り当て
        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'assigned_at' => now(),
        ]);
        
        $command = new RevokeRoleCommand(
            userId: $user->id,
            roleId: $role->ulid,
        );
        
        // トランザクション中にエラーを発生させるためのモック設定
        $this->app->bind(RoleAssignmentService::class, function () {
            $mock = $this->mock(RoleAssignmentService::class);
            $mock->shouldReceive('revokeRole')
                ->andThrow(new Exception('Test exception'));
            return $mock;
        });
        
        // Action を再取得（モックが適用されるように）
        $action = app(RevokeRoleAction::class);
        
        // Act & Assert
        try {
            $action($command);
            $this->fail('Exception should have been thrown');
        } catch (Exception $e) {
            // トランザクションがロールバックされたか確認
            $this->assertDatabaseHas('user_roles', [
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);
        }
    }

    #[Test]
    public function 正常系_剥奪後権限確認(): void
    {
        // Arrange
        $user = User::factory()->create();
        $adminRole = Role::factory()->create([
            'name' => 'admin',
        ]);
        $userRole = Role::factory()->create([
            'name' => 'user',
        ]);
        
        // 両方のロールを割り当て
        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $adminRole->id,
            'assigned_at' => now(),
        ]);
        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $userRole->id,
            'assigned_at' => now(),
        ]);
        
        $command = new RevokeRoleCommand(
            userId: $user->id,
            roleId: $adminRole->ulid,
        );
        
        // Act
        ($this->action)($command);
        
        // Assert - adminロールが剥奪され、userロールのみ残る
        $this->assertDatabaseMissing('user_roles', [
            'user_id' => $user->id,
            'role_id' => $adminRole->id,
        ]);
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $user->id,
            'role_id' => $userRole->id,
        ]);
    }

    #[Test]
    public function 境界値_最後のロール剥奪(): void
    {
        // Arrange
        $user = User::factory()->create();
        $role = Role::factory()->create();
        
        // ロールを一つだけ割り当て
        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'assigned_at' => now(),
        ]);
        
        $command = new RevokeRoleCommand(
            userId: $user->id,
            roleId: $role->ulid,
        );
        
        // Act
        ($this->action)($command);
        
        // Assert - ユーザーのロールが一つもない状態
        $this->assertEquals(0, UserRole::where('user_id', $user->id)->count());
    }
}