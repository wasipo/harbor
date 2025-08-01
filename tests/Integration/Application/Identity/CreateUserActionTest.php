<?php

namespace Tests\Integration\Application\Identity;

use App\Adapter\Identity\CreateUserCommand;
use App\Application\Identity\CreateUserAction;
use App\Adapter\Identity\UserOutputDTO;
use App\Domain\Shared\Collections\IdCollection;
use App\Infrastructure\Persistence\Eloquent\User as EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateUserAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = app(CreateUserAction::class);
    }

    public function test_正常系_ユーザー作成成功()
    {
        // Arrange
        $command = new CreateUserCommand(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password123',
            isActive: true,
            categoryIds: new IdCollection([]),
            roleIds: new IdCollection([])
        );

        // Act
        $result = $this->action->execute($command);

        // Assert
        $this->assertInstanceOf(UserOutputDTO::class, $result);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
        $this->assertTrue($result->is_active);

        // DB確認（パスワード欠落バグ修正確認）
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_active' => true,
        ]);

        // 重要：パスワードがnullでないことを確認
        $user = EloquentUser::where('email', 'test@example.com')->first();
        $this->assertNotNull($user->password);
        $this->assertNotEquals('', $user->password);
        $this->assertTrue(password_verify('password123', $user->password));
    }

    public function test_異常系_重複メールエラー()
    {
        // Arrange
        EloquentUser::factory()->create(['email' => 'existing@example.com']);

        $command = new CreateUserCommand(
            name: 'Duplicate User',
            email: 'existing@example.com',
            password: 'password123',
            isActive: true,
            categoryIds: new IdCollection([]),
            roleIds: new IdCollection([])
        );

        // Act & Assert
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Email already exists');

        try {
            $this->action->execute($command);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(422, $e->getStatusCode());
            throw $e;
        }
    }

    public function test_正常系_カテゴリロール割当あり()
    {
        // Arrange - Factoryを使用してSeeder依存を排除
        $category = \App\Infrastructure\Persistence\Eloquent\UserCategory::factory()->create();
        $role = \App\Infrastructure\Persistence\Eloquent\Role::factory()->create();

        $command = new CreateUserCommand(
            name: 'User With Relations',
            email: 'relations@example.com',
            password: 'password123',
            isActive: true,
            categoryIds: new IdCollection([$category->id]),
            roleIds: new IdCollection([$role->id])
        );

        // Act
        $result = $this->action->execute($command);

        // Assert
        $this->assertInstanceOf(UserOutputDTO::class, $result);
        $this->assertNotEmpty($result->categories);
        $this->assertNotEmpty($result->roles);

        // DB確認（関連テーブル）
        $this->assertDatabaseHas('user_category_assignments', [
            'category_id' => $category->id,
        ]);
        $this->assertDatabaseHas('user_roles', [
            'role_id' => $role->id,
        ]);
    }
}
