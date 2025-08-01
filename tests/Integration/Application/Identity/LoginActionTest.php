<?php

namespace Tests\Integration\Application\Identity;

use App\Adapter\Identity\AuthOutputDTO;
use App\Application\Identity\LoginAction;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User as EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\UnitTestCase;

class LoginActionTest extends UnitTestCase
{
    use RefreshDatabase;

    private LoginAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = app(LoginAction::class);
    }

    /**
     * Create a mock LoginRequest with given data
     *
     * @param  array<string, mixed>  $data
     */
    private function createLoginRequest(array $data): LoginRequest
    {
        $request = LoginRequest::create('/', 'POST', $data);
        $request->setContainer($this->app);
        $request->validateResolved();

        return $request;
    }

    public function test_正常系_ログイン成功(): void
    {
        // Arrange
        EloquentUser::factory()->create([
            'id' => '01HZKT234567890ABCDEFGHIJK',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $request = $this->createLoginRequest([
            'email' => 'test@example.com',
            'password' => 'password123',
            'remember' => false,
        ]);

        // Act
        $result = ($this->action)($request);

        // Assert
        $this->assertInstanceOf(AuthOutputDTO::class, $result);
        $this->assertNotEmpty($result->token);
        $this->assertInstanceOf(\DateTimeInterface::class, $result->expires_at);
        $this->assertEquals('test@example.com', $result->user->email);
    }

    public function test_異常系_認証失敗_無効なパスワード(): void
    {
        // Arrange
        EloquentUser::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct_password'),
            'is_active' => true,
        ]);

        $request = $this->createLoginRequest([
            'email' => 'test@example.com',
            'password' => 'wrong_password',
            'remember' => false,
        ]);

        // Act & Assert
        $this->expectException(ValidationException::class);
        ($this->action)($request);
    }

    public function test_異常系_認証失敗_存在しないメール(): void
    {
        // Arrange
        $request = $this->createLoginRequest([
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
            'remember' => false,
        ]);

        // Act & Assert
        $this->expectException(ValidationException::class);
        ($this->action)($request);
    }

    public function test_異常系_非アクティブユーザー(): void
    {
        // Arrange
        EloquentUser::factory()->create([
            'email' => 'inactive@example.com',
            'password' => bcrypt('password123'),
            'is_active' => false, // 非アクティブ
        ]);

        $request = $this->createLoginRequest([
            'email' => 'inactive@example.com',
            'password' => 'password123',
            'remember' => false,
        ]);

        // Act & Assert
        $this->expectException(ValidationException::class);
        ($this->action)($request);
    }

    public function test_正常系_remember機能(): void
    {
        // Arrange
        EloquentUser::factory()->create([
            'email' => 'remember@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $request = $this->createLoginRequest([
            'email' => 'remember@example.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        // Act
        $result = ($this->action)($request);

        // Assert
        $this->assertInstanceOf(AuthOutputDTO::class, $result);
        $this->assertNotEmpty($result->token);
    }
}
