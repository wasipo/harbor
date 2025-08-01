<?php

namespace Tests\Feature\AccessControl\Role;

use App\Adapter\AccessControl\Role\AssignRoleCommand;
use App\Application\AccessControl\Role\AssignRoleAction;
use App\Domain\AccessControl\Role\RoleAssignmentService;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use DomainException;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\FeatureTestCase;

class AssignRoleActionTest extends FeatureTestCase
{
    use RefreshDatabase;

    private AssignRoleAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = app(AssignRoleAction::class);
    }

    #[Test]
    public function 正常系_ロール割り当て成功(): void
    {
        // Arrange
        $user = User::factory()->create([
            'is_active' => true,
        ]);
        $role = Role::factory()->create();

        $command = new AssignRoleCommand(
            userId: $user->id,
            roleId: $role->id,
        );

        // Act
        ($this->action)($command);

        // Assert
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    #[Test]
    public function 正常系_割り当て者記録(): void
    {
        // Arrange
        $user = User::factory()->create([
            'is_active' => true,
        ]);
        $assignedBy = User::factory()->create();
        $role = Role::factory()->create();

        $command = new AssignRoleCommand(
            userId: $user->id,
            roleId: $role->id,
            assignedByUserId: $assignedBy->id,
        );

        // Act
        ($this->action)($command);

        // Assert
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $user->id,
            'role_id' => $role->id,
            'assigned_by' => $assignedBy->id,
        ]);
    }

    #[Test]
    public function 正常系_割り当て者なし(): void
    {
        // Arrange
        $user = User::factory()->create([
            'is_active' => true,
        ]);
        $role = Role::factory()->create();

        $command = new AssignRoleCommand(
            userId: $user->id,
            roleId: $role->id,
            assignedByUserId: null,
        );

        // Act
        ($this->action)($command);

        // Assert
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $user->id,
            'role_id' => $role->id,
            'assigned_by' => null,
        ]);
    }

    #[Test]
    public function 異常系_ユーザー不在エラー(): void
    {
        // Arrange
        $role = Role::factory()->create();

        $command = new AssignRoleCommand(
            userId: '01HJ0YXPQR8X5M0TH5BCKZ3WEF',
            roleId: $role->id,
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
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $command = new AssignRoleCommand(
            userId: $user->id,
            roleId: Str::ulid()->toString(),
        );

        // Act & Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Role not found');

        ($this->action)($command);
    }

    #[Test]
    public function 異常系_非アクティブユーザーエラー(): void
    {
        // Arrange
        $user = User::factory()->create([
            'is_active' => false,
        ]);
        $role = Role::factory()->create();

        $command = new AssignRoleCommand(
            userId: $user->id,
            roleId: $role->id,
        );

        // Act & Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot assign role to inactive user');

        ($this->action)($command);
    }

    #[Test]
    public function 正常系_トランザクションロールバック(): void
    {
        // Arrange
        $user = User::factory()->create([
            'is_active' => true,
        ]);
        $role = Role::factory()->create();

        $command = new AssignRoleCommand(
            userId: $user->id,
            roleId: $role->id,
        );

        // トランザクション中にエラーを発生させるためのモック設定
        $this->app->bind(RoleAssignmentService::class, function () {
            $mock = $this->mock(RoleAssignmentService::class);
            // todo: mockeryをstanが完全に理解してないため無視(型拡張をいつかやる)
            // @phpstan-ignore-next-line
            $mock->shouldReceive('assignRole')
                ->andThrow(new Exception('Test exception'));

            return $mock;
        });

        // Action を再取得（モックが適用されるように）
        $action = app(AssignRoleAction::class);

        // Act & Assert
        try {
            $action($command);
            $this->fail('Exception should have been thrown');
        } catch (Exception $e) {
            // トランザクションがロールバックされたか確認
            $this->assertDatabaseMissing('user_roles', [
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);
        }
    }

    #[Test]
    public function 境界値_複数ロール同時割り当て(): void
    {
        // Arrange
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $roles = [];
        for ($i = 0; $i < 5; $i++) {
            $roles[] = Role::factory()->create();
        }

        // Act
        foreach ($roles as $role) {
            $command = new AssignRoleCommand(
                userId: $user->id,
                roleId: $role->id,
            );
            ($this->action)($command);
        }

        // Assert
        $this->assertEquals(5, UserRole::where('user_id', $user->id)->count());
        foreach ($roles as $role) {
            $this->assertDatabaseHas('user_roles', [
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);
        }
    }
}
