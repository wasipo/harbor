<?php

namespace Tests\Integration\Application\Identity;

use App\Application\Identity\LoginAction;
use App\Adapter\Identity\AuthOutputDTO;
use App\Models\User as EloquentUser;
use App\Application\Identity\LoginActionValuesInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginActionTest extends TestCase
{
    use RefreshDatabase;

    private LoginAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = app(LoginAction::class);
    }

    public function test_正常系_ログイン成功()
    {
        // Arrange
        EloquentUser::factory()->create([
            'ulid' => '01HZKT234567890ABCDEFGHIJK',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $request = new class implements LoginActionValuesInterface
        {
            public function email(): string
            {
                return 'test@example.com';
            }

            public function password(): string
            {
                return 'password123';
            }

            public function remember(): bool
            {
                return false;
            }

            public function xsrf(): ?string
            {
                return null;
            }

            public function idempotencyKey(): ?string
            {
                return null;
            }
        };

        // Act
        $result = ($this->action)($request);

        // Assert
        $this->assertInstanceOf(AuthOutputDTO::class, $result);
        $this->assertNotEmpty($result->token);
        $this->assertNotNull($result->expires_at);
        $this->assertEquals('test@example.com', $result->user->email);
    }

    public function test_異常系_認証失敗_無効なパスワード()
    {
        // Arrange
        EloquentUser::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct_password'),
            'is_active' => true,
        ]);

        $request = new class implements LoginActionValuesInterface
        {
            public function email(): string
            {
                return 'test@example.com';
            }

            public function password(): string
            {
                return 'wrong_password';
            }

            public function remember(): bool
            {
                return false;
            }

            public function xsrf(): ?string
            {
                return null;
            }

            public function idempotencyKey(): ?string
            {
                return null;
            }
        };

        // Act & Assert
        $this->expectException(ValidationException::class);
        ($this->action)($request);
    }

    public function test_異常系_認証失敗_存在しないメール()
    {
        // Arrange
        $request = new class implements LoginActionValuesInterface
        {
            public function email(): string
            {
                return 'nonexistent@example.com';
            }

            public function password(): string
            {
                return 'password123';
            }

            public function remember(): bool
            {
                return false;
            }

            public function xsrf(): ?string
            {
                return null;
            }

            public function idempotencyKey(): ?string
            {
                return null;
            }
        };

        // Act & Assert
        $this->expectException(ValidationException::class);
        ($this->action)($request);
    }

    public function test_異常系_非アクティブユーザー()
    {
        // Arrange
        EloquentUser::factory()->create([
            'email' => 'inactive@example.com',
            'password' => bcrypt('password123'),
            'is_active' => false, // 非アクティブ
        ]);

        $request = new class implements LoginActionValuesInterface
        {
            public function email(): string
            {
                return 'inactive@example.com';
            }

            public function password(): string
            {
                return 'password123';
            }

            public function remember(): bool
            {
                return false;
            }

            public function xsrf(): ?string
            {
                return null;
            }

            public function idempotencyKey(): ?string
            {
                return null;
            }
        };

        // Act & Assert
        $this->expectException(ValidationException::class);
        ($this->action)($request);
    }

    public function test_正常系_remember機能()
    {
        // Arrange
        EloquentUser::factory()->create([
            'email' => 'remember@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $request = new class implements LoginActionValuesInterface
        {
            public function email(): string
            {
                return 'remember@example.com';
            }

            public function password(): string
            {
                return 'password123';
            }

            public function remember(): bool
            {
                return true;
            }

            public function xsrf(): ?string
            {
                return null;
            }

            public function idempotencyKey(): ?string
            {
                return null;
            }
        };

        // Act
        $result = ($this->action)($request);

        // Assert
        $this->assertInstanceOf(AuthOutputDTO::class, $result);
        $this->assertNotEmpty($result->token);
    }
}
